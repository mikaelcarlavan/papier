<?php

declare(strict_types=1);

namespace Papier\Metadata;

use Papier\Objects\{PdfDictionary, PdfName, PdfStream};

/**
 * XMP metadata stream (ISO 32000-1 §14.3.2).
 *
 * XMP (Extensible Metadata Platform) embeds document metadata as XML.
 * The stream is always unencrypted and uncompressed for interoperability.
 */
final class XmpMetadata
{
    private PdfStream $stream;

    public function __construct(
        private string  $title    = '',
        private string  $author   = '',
        private string  $subject  = '',
        private string  $keywords = '',
        private string  $creator  = '',
        private string  $producer = 'Papier PDF Library',
        private ?\DateTimeInterface $creationDate = null,
        private ?\DateTimeInterface $modDate      = null,
    ) {
        $this->creationDate ??= new \DateTime();
        $this->modDate      ??= new \DateTime();
        $this->stream = new PdfStream();
        $this->stream->getDictionary()->set('Type', new PdfName('Metadata'));
        $this->stream->getDictionary()->set('Subtype', new PdfName('XML'));
        // XMP must not be filtered (§14.3.2)
    }

    public function setTitle(string $t): static  { $this->title    = $t; return $this; }
    public function setAuthor(string $a): static  { $this->author   = $a; return $this; }
    public function setSubject(string $s): static { $this->subject  = $s; return $this; }
    public function setKeywords(string $k): static{ $this->keywords = $k; return $this; }
    public function setCreator(string $c): static { $this->creator  = $c; return $this; }
    public function setProducer(string $p): static{ $this->producer = $p; return $this; }

    public function getStream(): PdfStream
    {
        $this->stream->setData($this->buildXml());
        return $this->stream;
    }

    private function buildXml(): string
    {
        $cd = $this->creationDate->format('c');
        $md = $this->modDate->format('c');
        $e  = fn(string $s) => htmlspecialchars($s, ENT_XML1 | ENT_QUOTES, 'UTF-8');

        return <<<XML
<?xpacket begin="\xef\xbb\xbf" id="W5M0MpCehiHzreSzNTczkc9d"?>
<x:xmpmeta xmlns:x="adobe:ns:meta/">
<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
<rdf:Description rdf:about=""
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:pdf="http://ns.adobe.com/pdf/1.3/"
    xmlns:xmp="http://ns.adobe.com/xap/1.0/">
  <dc:title><rdf:Alt><rdf:li xml:lang="x-default">{$e($this->title)}</rdf:li></rdf:Alt></dc:title>
  <dc:creator><rdf:Seq><rdf:li>{$e($this->author)}</rdf:li></rdf:Seq></dc:creator>
  <dc:description><rdf:Alt><rdf:li xml:lang="x-default">{$e($this->subject)}</rdf:li></rdf:Alt></dc:description>
  <dc:subject><rdf:Bag><rdf:li>{$e($this->keywords)}</rdf:li></rdf:Bag></dc:subject>
  <pdf:Producer>{$e($this->producer)}</pdf:Producer>
  <pdf:Keywords>{$e($this->keywords)}</pdf:Keywords>
  <xmp:CreatorTool>{$e($this->creator)}</xmp:CreatorTool>
  <xmp:CreateDate>{$cd}</xmp:CreateDate>
  <xmp:ModifyDate>{$md}</xmp:ModifyDate>
</rdf:Description>
</rdf:RDF>
</x:xmpmeta>
<?xpacket end="w"?>
XML;
    }
}
