<?php

declare(strict_types=1);

namespace Papier\Tests;

use PHPUnit\Framework\TestCase;
use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Parser\PdfParser;
use Papier\Signature\{PdfSigner, DocumentTimestamp};

final class SignatureFeaturesTest extends TestCase
{
    private string $certPem = '';
    private string $keyPem  = '';

    protected function setUp(): void
    {
        $pkey = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        if ($pkey === false) {
            $this->markTestSkipped('OpenSSL unavailable.');
        }
        $csr  = openssl_csr_new(['commonName' => 'Papier Test'], $pkey, ['digest_alg' => 'sha256']);
        $x509 = openssl_csr_sign($csr, null, $pkey, 365, ['digest_alg' => 'sha256']);
        openssl_x509_export($x509, $this->certPem);
        openssl_pkey_export($pkey, $this->keyPem);
    }

    private function doc(): string
    {
        $d = PdfDocument::create();
        $d->setTitle('Signable');
        $f = $d->addFont('Helvetica');
        $p = $d->addPage();
        $p->add(Text::write('Please sign here.')->at(72, 750)->font($f, 14));
        return $d->toString();
    }

    public function testVisibleSignatureAppearance(): void
    {
        $signer = (new PdfSigner($this->certPem, $this->keyPem))
            ->setName('Alice Example')->setReason('Approved')->setLocation('Paris')
            ->setVisibleAppearance(72, 600, 220, 70, 1);
        $signed = $signer->sign($this->doc());

        // A visible widget with a non-empty rect and an appearance stream.
        $this->assertStringContainsString('/Subtype /Form', $signed);
        $this->assertStringContainsString('(Signed by: Alice Example) Tj', $signed);
        $this->assertStringContainsString('(Reason: Approved) Tj', $signed);

        // The widget rect is no longer [0 0 0 0].
        $this->assertMatchesRegularExpression('/\/Rect \[72(\.0+)? 600/', $signed);

        // Still a valid, parseable, byte-range-protected signature.
        $this->assertStringContainsString('/SubFilter /adbe.pkcs7.detached', $signed);
        $parser = new PdfParser($signed);
        $parser->parse();
        $this->assertSame('Signable', $parser->getTitle());
    }

    public function testDocumentTimestampStructure(): void
    {
        // Sign first, then add a document timestamp using a mock TSA client.
        $signed = (new PdfSigner($this->certPem, $this->keyPem))->sign($this->doc());

        $tokenSeen = '';
        $mockToken = "\x30\x0A\x06\x08mocktok!"; // a small DER-ish SEQUENCE
        $ts = new DocumentTimestamp(function (string $digest) use (&$tokenSeen, $mockToken): string {
            $tokenSeen = $digest;
            $this->assertSame(32, strlen($digest)); // SHA-256
            return $mockToken;
        });
        $stamped = $ts->apply($signed);

        // DocTimeStamp revision present and incremental.
        $this->assertStringContainsString('/Type /DocTimeStamp', $stamped);
        $this->assertStringContainsString('/SubFilter /ETSI.RFC3161', $stamped);
        $this->assertStringStartsWith($signed, $stamped);
        $this->assertSame(3, substr_count($stamped, '%%EOF')); // original + sig + timestamp
        $this->assertNotSame('', $tokenSeen);
        $this->assertStringContainsString(bin2hex($mockToken), $stamped);
    }

    public function testBuildRequestIsValidDer(): void
    {
        $digest = hash('sha256', 'hello', true);
        $req = DocumentTimestamp::buildRequest($digest);

        // Outer SEQUENCE.
        $this->assertSame(0x30, ord($req[0]));
        // Contains the SHA-256 OID and the digest.
        $this->assertStringContainsString("\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x01", $req);
        $this->assertStringContainsString($digest, $req);
    }

    public function testExtractTokenFromResp(): void
    {
        // TimeStampResp = SEQUENCE { status SEQUENCE{...}, token SEQUENCE{...} }
        $status = "\x30\x03\x02\x01\x00";          // SEQUENCE { INTEGER 0 }
        $token  = "\x30\x06\x06\x04test";          // SEQUENCE (the token)
        $resp   = "\x30" . chr(strlen($status) + strlen($token)) . $status . $token;

        $this->assertSame($token, DocumentTimestamp::extractToken($resp));
    }
}
