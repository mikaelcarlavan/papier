<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Parser\PdfParser;

final class FuzzAndCorpusTest extends TestCase
{
    private string $tmp;

    protected function setUp(): void
    {
        $this->tmp = sys_get_temp_dir() . '/papier_corpus_' . getmypid();
        @mkdir($this->tmp, 0777, true);
    }

    private function base(): string
    {
        $doc  = PdfDocument::create();
        $doc->setTitle('Corpus')->setAuthor('Papier');
        $font = $doc->addFont('Helvetica');
        foreach ([1, 2, 3] as $n) {
            $doc->addPage()->add(Text::write("Corpus page $n")->at(72, 750)->font($font, 14));
        }
        return $doc->toString();
    }

    private function qpdf(): ?string
    {
        $path = trim((string) shell_exec('command -v qpdf 2>/dev/null'));
        return $path !== '' ? $path : null;
    }

    /**
     * Parse PDFs produced by an independent producer (qpdf) in several shapes.
     */
    public function testReadsQpdfProducedVariants(): void
    {
        if ($this->qpdf() === null) {
            $this->markTestSkipped('qpdf not available.');
        }
        $base = $this->tmp . '/base.pdf';
        file_put_contents($base, $this->base());

        $variants = [
            'objstm'     => '--object-streams=generate',
            'linear'     => '--linearize',
            'qdf'        => '--qdf --object-streams=disable',  // indirect /Length
            'recompress' => '--compress-streams=y --recompress-flate',
        ];
        foreach ($variants as $name => $opts) {
            $out = $this->tmp . "/$name.pdf";
            exec('qpdf ' . $opts . ' ' . escapeshellarg($base) . ' ' . escapeshellarg($out) . ' 2>&1', $o, $rc);
            $this->assertLessThanOrEqual(3, $rc, "qpdf $name failed"); // 0 ok, 3 warnings
            $this->assertFileExists($out);

            $parser = new PdfParser(file_get_contents($out));
            $parser->parse();
            $this->assertSame(3, $parser->getPageCount(), "page count ($name)");
            $this->assertStringContainsString('Corpus page 2', $parser->extractText(), "text ($name)");
        }
    }

    /**
     * Read an AES-256 document encrypted by qpdf — an independent encryptor,
     * validating our decryptor end-to-end. (RC4/AES-128 are covered by our own
     * writer↔reader round-trips; modern qpdf refuses to emit weak ciphers.)
     */
    public function testReadsQpdfEncryption(): void
    {
        if ($this->qpdf() === null) {
            $this->markTestSkipped('qpdf not available.');
        }
        $base = $this->tmp . '/base_enc.pdf';
        file_put_contents($base, $this->base());

        $out = $this->tmp . '/enc256.pdf';
        exec('qpdf --encrypt secret owner 256 -- '
            . escapeshellarg($base) . ' ' . escapeshellarg($out) . ' 2>&1', $o, $rc);
        if (!is_file($out) || filesize($out) === 0) {
            $this->markTestSkipped('qpdf could not produce an encrypted file.');
        }

        // User password.
        $parser = (new PdfParser(file_get_contents($out)))->setPassword('secret');
        $parser->parse();
        $this->assertSame('Corpus', $parser->getTitle());
        $this->assertSame(3, $parser->getPageCount());

        // Owner password.
        $owner = (new PdfParser(file_get_contents($out)))->setPassword('owner');
        $owner->parse();
        $this->assertSame('Corpus', $owner->getTitle());
    }

    /**
     * Truncated documents must fail gracefully (no fatal, no hang), never crash.
     */
    public function testTruncationsDoNotCrash(): void
    {
        $pdf = $this->base();
        $len = strlen($pdf);
        for ($cut = 1; $cut <= 40; $cut++) {
            $truncated = substr($pdf, 0, (int) ($len * $cut / 40));
            $this->parseQuietly($truncated);
        }
        $this->addToAssertionCount(1); // reached here without fatal/hang
    }

    /**
     * Random single-byte corruptions must not crash the parser.
     */
    public function testByteFlipsDoNotCrash(): void
    {
        $pdf = $this->base();
        $len = strlen($pdf);
        mt_srand(20260628); // deterministic
        for ($i = 0; $i < 300; $i++) {
            $bytes = $pdf;
            $pos = mt_rand(0, $len - 1);
            $bytes[$pos] = chr(mt_rand(0, 255));
            $this->parseQuietly($bytes);
        }
        $this->addToAssertionCount(1);
    }

    /** Parse without letting any exception/error escape. */
    private function parseQuietly(string $bytes): void
    {
        try {
            $parser = new PdfParser($bytes);
            $parser->parse();
            // Exercise the read paths too.
            $parser->getPageCount();
            $parser->extractText();
            $parser->getMetadata();
        } catch (\Throwable) {
            // A malformed file is allowed to throw — it must not fatal or hang.
        }
    }
}
