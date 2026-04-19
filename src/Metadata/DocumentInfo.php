<?php

declare(strict_types=1);

namespace Papier\Metadata;

use Papier\Objects\{PdfDictionary, PdfString};

/**
 * Document information dictionary (ISO 32000-1 §14.3.3 Table 317).
 *
 * Contains metadata about the document: title, author, subject, keywords,
 * creator, producer, creation date, and modification date.
 *
 * Dates are in PDF date format: D:YYYYMMDDHHmmSSZ (§7.9.4).
 */
final class DocumentInfo
{
    private PdfDictionary $dict;

    public function __construct()
    {
        $this->dict = new PdfDictionary();
    }

    public function setTitle(string $title): static
    {
        $this->dict->set('Title', PdfString::text($title));
        return $this;
    }

    public function setAuthor(string $author): static
    {
        $this->dict->set('Author', PdfString::text($author));
        return $this;
    }

    public function setSubject(string $subject): static
    {
        $this->dict->set('Subject', PdfString::text($subject));
        return $this;
    }

    public function setKeywords(string $keywords): static
    {
        $this->dict->set('Keywords', PdfString::text($keywords));
        return $this;
    }

    public function setCreator(string $creator): static
    {
        $this->dict->set('Creator', PdfString::text($creator));
        return $this;
    }

    public function setProducer(string $producer): static
    {
        $this->dict->set('Producer', PdfString::text($producer));
        return $this;
    }

    /**
     * Set the creation date.
     *
     * @param \DateTimeInterface|null $date  Default: current time.
     */
    public function setCreationDate(?\DateTimeInterface $date = null): static
    {
        $date ??= new \DateTime();
        $this->dict->set('CreationDate', new PdfString($this->formatDate($date)));
        return $this;
    }

    public function setModDate(?\DateTimeInterface $date = null): static
    {
        $date ??= new \DateTime();
        $this->dict->set('ModDate', new PdfString($this->formatDate($date)));
        return $this;
    }

    public function setTrapped(string $trapped = 'Unknown'): static
    {
        $this->dict->set('Trapped', new \Papier\Objects\PdfName($trapped));
        return $this;
    }

    public function getDictionary(): PdfDictionary { return $this->dict; }

    /**
     * Format a date as a PDF date string: D:YYYYMMDDHHmmSSOHH'mm'.
     */
    public static function formatDate(\DateTimeInterface $date): string
    {
        $tz     = $date->getTimezone();
        $offset = $date->getOffset(); // seconds
        if ($offset === 0) {
            $tzStr = 'Z';
        } else {
            $sign   = $offset > 0 ? '+' : '-';
            $offset = abs($offset);
            $hours  = intdiv($offset, 3600);
            $mins   = ($offset % 3600) / 60;
            $tzStr  = sprintf("%s%02d'%02d'", $sign, $hours, $mins);
        }
        return "D:" . $date->format('YmdHis') . $tzStr;
    }
}
