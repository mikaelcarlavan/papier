<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Parser\PdfParser;
use Papier\Signature\PdfSigner;

final class PdfSignerTest extends TestCase
{
    private string $certPem = '';
    private string $keyPem  = '';

    protected function setUp(): void
    {
        $pkey = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        if ($pkey === false) {
            $this->markTestSkipped('OpenSSL key generation unavailable in this environment.');
        }
        $csr  = openssl_csr_new(['commonName' => 'Papier Test', 'organizationName' => 'Papier'], $pkey, ['digest_alg' => 'sha256']);
        if ($csr === false) {
            $this->markTestSkipped('OpenSSL CSR generation unavailable.');
        }
        $x509 = openssl_csr_sign($csr, null, $pkey, 365, ['digest_alg' => 'sha256']);
        openssl_x509_export($x509, $this->certPem);
        openssl_pkey_export($pkey, $this->keyPem);
    }

    private function unsigned(): string
    {
        $doc  = PdfDocument::create();
        $doc->setTitle('Contract');
        $font = $doc->addFont('Helvetica');
        $page = $doc->addPage();
        $page->add(Text::write('I agree to the terms.')->at(72, 750)->font($font, 14));
        return $doc->toString();
    }

    public function testSignAddsValidSignatureStructure(): void
    {
        $original = $this->unsigned();
        $signer = (new PdfSigner($this->certPem, $this->keyPem))
            ->setReason('Approved')->setLocation('Paris')->setName('Papier Test');
        $signed = $signer->sign($original);

        // Incremental: original preserved as a byte-for-byte prefix.
        $this->assertStringStartsWith($original, $signed);
        $this->assertStringContainsString('/SubFilter /adbe.pkcs7.detached', $signed);
        $this->assertStringContainsString('/Type /Sig', $signed);
        $this->assertStringContainsString('/SigFlags 3', $signed);
        $this->assertSame(2, substr_count($signed, '%%EOF'));

        // ByteRange spans the whole file except the /Contents hex gap.
        $this->assertSame(1, preg_match('/\/ByteRange \[(\d+) (\d+) (\d+) (\d+)\]/', $signed, $br));
        [, $a, $b, $c, $d] = array_map('intval', $br);
        $this->assertSame(0, $a);
        $this->assertSame(strlen($signed), $c + $d);
        // The excluded gap equals the reserved hex string plus the angle brackets.
        $this->assertSame($b, strpos($signed, '/Contents <') + strlen('/Contents '));
    }

    public function testSignatureContentsIsValidDetachedPkcs7(): void
    {
        $signed = (new PdfSigner($this->certPem, $this->keyPem))->sign($this->unsigned());

        preg_match('/\/ByteRange \[(\d+) (\d+) (\d+) (\d+)\]/', $signed, $br);
        [, $a, $b, $c, $d] = array_map('intval', $br);
        $signedData = substr($signed, $a, $b) . substr($signed, $c, $d);

        $cpos = strpos($signed, '/Contents <') + strlen('/Contents <');
        $end  = strpos($signed, '>', $cpos);
        $hex  = substr($signed, $cpos, $end - $cpos);
        $der  = hex2bin(rtrim($hex, '0') . (strlen(rtrim($hex, '0')) % 2 ? '0' : ''));

        // DER: SEQUENCE containing the PKCS#7 signedData OID 1.2.840.113549.1.7.2.
        $this->assertSame(0x30, ord($der[0]));
        $this->assertStringContainsString(hex2bin('06092a864886f70d010702'), $der);

        // Cryptographic verification via the OpenSSL CLI, when available.
        if (!function_exists('exec')) {
            return;
        }
        $dir = sys_get_temp_dir();
        $df = tempnam($dir, 'd'); $sf = tempnam($dir, 's'); $cf = tempnam($dir, 'c');
        file_put_contents($df, $signedData);
        file_put_contents($sf, $der);
        file_put_contents($cf, $this->certPem);
        $out = []; $rc = 0;
        exec('openssl smime -verify -binary -inform DER -in ' . escapeshellarg($sf)
            . ' -content ' . escapeshellarg($df) . ' -noverify -certfile ' . escapeshellarg($cf)
            . ' -out /dev/null 2>&1', $out, $rc);
        @unlink($df); @unlink($sf); @unlink($cf);
        if ($rc === 127) {
            return; // openssl CLI not installed
        }
        $this->assertSame(0, $rc, 'openssl verification failed: ' . implode(' ', $out));
    }

    public function testSignedPdfStillParses(): void
    {
        $signed = (new PdfSigner($this->certPem, $this->keyPem))->sign($this->unsigned());
        $parser = new PdfParser($signed);
        $parser->parse();
        $this->assertSame(1, $parser->getPageCount());
        $this->assertSame('Contract', $parser->getTitle());
        $this->assertStringContainsString('I agree', $parser->extractText());
    }
}
