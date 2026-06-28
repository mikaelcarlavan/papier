<?php

/**
 * Example 32: Visible signature appearance + document timestamp (PAdES)
 *
 * Adds a visible signature box (name / reason / location / date) and then a
 * document timestamp (/DocTimeStamp, RFC 3161). The timestamp token is obtained
 * via a caller-supplied TSA client — here a mock is used so the example runs
 * offline; in production, POST DocumentTimestamp::buildRequest() to a real TSA.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Signature\{PdfSigner, DocumentTimestamp};

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

// Document to sign.
$doc  = PdfDocument::create();
$doc->setTitle('Signed Agreement');
$font = $doc->addFont('Helvetica');
$page = $doc->addPage();
$page->add(Text::write('This agreement is digitally signed and timestamped.')->at(72, 750)->font($font, 13));
$unsigned = $doc->toString();

// Self-signed certificate (demo only).
$pkey = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
if ($pkey === false) { echo "OpenSSL unavailable.\n"; exit(1); }
$csr  = openssl_csr_new(['commonName' => 'Papier Demo', 'organizationName' => 'Papier'], $pkey, ['digest_alg' => 'sha256']);
$x509 = openssl_csr_sign($csr, null, $pkey, 365, ['digest_alg' => 'sha256']);
openssl_x509_export($x509, $certPem);
openssl_pkey_export($pkey, $keyPem);

// 1. Sign with a VISIBLE appearance box.
$signer = (new PdfSigner($certPem, $keyPem))
    ->setName('Alice Example')
    ->setReason('I approve this agreement')
    ->setLocation('Paris')
    ->setVisibleAppearance(x: 360, y: 690, w: 170, h: 60, page: 1);
$signed = $signer->sign($unsigned);
echo "Signed (visible appearance): " . number_format(strlen($signed)) . " bytes\n";

// 2. Add a document timestamp using a MOCK TSA client (offline demo).
//    A real client would POST DocumentTimestamp::buildRequest($digest) to a TSA
//    and return DocumentTimestamp::extractToken($response).
$ts = new DocumentTimestamp(function (string $digest): string {
    $request = DocumentTimestamp::buildRequest($digest);     // valid RFC 3161 request (DER)
    // ... POST $request to your TSA here ...
    return "\x30\x0A\x06\x08demo-tsa";                        // mock token (DER-ish)
});
$timestamped = $ts->apply($signed);

$file = "$outDir/32_signed.pdf";
file_put_contents($file, $timestamped);
echo "Created: 32_signed.pdf (" . number_format(filesize($file)) . " bytes)\n\n";

echo "Visible appearance widget: " . (str_contains($timestamped, '/Subtype /Form') ? 'yes' : 'no') . "\n";
echo "Approval signature:        " . (str_contains($timestamped, '/adbe.pkcs7.detached') ? 'yes' : 'no') . "\n";
echo "Document timestamp:        " . (str_contains($timestamped, '/DocTimeStamp') ? 'yes' : 'no') . "\n";
echo "Revisions (%%EOF):         " . substr_count($timestamped, '%%EOF') . "\n";

echo "\nDone.\n";
