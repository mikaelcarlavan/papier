<?php

declare(strict_types=1);

namespace Papier\Writer;

use Papier\Objects\{
    PdfArray, PdfDictionary, PdfIndirectReference, PdfInteger, PdfObject, PdfString
};
use Papier\Parser\PdfParser;

/**
 * Incremental update writer (ISO 32000-1 §7.5.6).
 *
 * Appends a new revision to an existing PDF without rewriting it: the original
 * bytes are preserved verbatim, followed by the changed/added objects, a new
 * cross-reference section, and a trailer whose /Prev points at the previous
 * cross-reference section.  This is the mechanism required for digital
 * signatures and for safely editing files you did not author.
 *
 * Example — change the title of an existing document:
 *
 *   $parser  = PdfParser::fromFile('in.pdf'); $parser->parse();
 *   $updater = new IncrementalUpdater($parser);
 *   $info    = clone-or-rebuild the Info dict ...
 *   $updater->updateObject($infoNum, $info);
 *   file_put_contents('out.pdf', $updater->build());
 */
final class IncrementalUpdater
{
    /** @var array<int, PdfObject> objNum → replacement/new object */
    private array $objects = [];

    private int $nextObjNum;

    public function __construct(private readonly PdfParser $parser)
    {
        $size = $this->parser->getXref()->getSize();
        $this->nextObjNum = max(1, $size);
    }

    /** Replace an existing object with a new value in this revision. */
    public function updateObject(int $objNum, PdfObject $obj): static
    {
        $this->objects[$objNum] = $obj;
        return $this;
    }

    /** Add a brand-new object and return its allocated object number. */
    public function addObject(PdfObject $obj): int
    {
        $num = $this->nextObjNum++;
        $this->objects[$num] = $obj;
        return $num;
    }

    /** Object number that {@see addObject()} will allocate next. */
    public function peekNextObjectNumber(): int { return $this->nextObjNum; }

    /**
     * Build the updated PDF bytes (original + appended revision).
     */
    public function build(): string
    {
        if (empty($this->objects)) {
            return $this->parser->getRawData();
        }

        $xref     = $this->parser->getXref();
        $original = $this->parser->getRawData();
        $prevXref = $xref->getStartXref() ?? 0;

        $out = $original;
        if ($out !== '' && substr($out, -1) !== "\n") {
            $out .= "\n";
        }

        // Append changed/new objects, recording absolute byte offsets.
        ksort($this->objects);
        $offsets = [];
        foreach ($this->objects as $num => $obj) {
            $offsets[$num] = strlen($out);
            $out .= "{$num} 0 obj\n" . $obj->toString() . "\nendobj\n";
        }

        $xrefOffset = strlen($out);
        $out .= $this->buildXRefSection($offsets);

        // Trailer.
        $size = max($xref->getSize(), $this->nextObjNum);
        $trailer = new PdfDictionary();
        $trailer->set('Size', new PdfInteger($size));

        $root = $xref->getCatalogObjectNumber();
        if ($root !== null) {
            $trailer->set('Root', new PdfIndirectReference($root));
        }
        $info = $xref->getInfoObjectNumber();
        if ($info !== null) {
            $trailer->set('Info', new PdfIndirectReference($info));
        }
        $encRef = $xref->getEncryptReference();
        if ($encRef instanceof PdfIndirectReference) {
            $trailer->set('Encrypt', $encRef);
        }
        $idArr = $xref->getIdArray();
        if ($idArr instanceof PdfArray) {
            // Re-emit the existing /ID (PDF requires it to be present and stable
            // across the first element).
            $newId = new PdfArray();
            foreach ($idArr->getItems() as $item) {
                if ($item instanceof PdfString) {
                    $newId->add(PdfString::hex($item->getValue()));
                } else {
                    $newId->add($item);
                }
            }
            $trailer->set('ID', $newId);
        }
        $trailer->set('Prev', new PdfInteger($prevXref));

        $out .= "trailer\n" . $trailer->toString();
        $out .= "\nstartxref\n{$xrefOffset}\n%%EOF\n";

        return $out;
    }

    /** Write the updated PDF to a file. */
    public function save(string $path): void
    {
        if (file_put_contents($path, $this->build()) === false) {
            throw new \RuntimeException("Cannot write PDF to: $path");
        }
    }

    /**
     * Build a classic cross-reference section for just the changed objects,
     * grouped into contiguous subsections (§7.5.4).
     *
     * @param array<int,int> $offsets objNum → byte offset
     */
    private function buildXRefSection(array $offsets): string
    {
        ksort($offsets);
        $nums = array_keys($offsets);

        // Group contiguous object numbers into subsections.
        $subsections = [];
        $start = $prev = null;
        $run   = [];
        foreach ($nums as $n) {
            if ($prev === null || $n === $prev + 1) {
                $run[] = $n;
            } else {
                $subsections[] = $run;
                $run = [$n];
            }
            if ($prev === null) {
                $start = $n;
            }
            $prev = $n;
        }
        if (!empty($run)) {
            $subsections[] = $run;
        }

        $xref = "xref\n";
        foreach ($subsections as $run) {
            $first = $run[0];
            $count = count($run);
            $xref .= "{$first} {$count}\n";
            foreach ($run as $n) {
                $xref .= sprintf("%010d 00000 n\r\n", $offsets[$n]);
            }
        }
        return $xref;
    }
}
