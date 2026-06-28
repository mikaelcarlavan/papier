<?php

/**
 * Example 23: Digital signatures (ISO 32000-1 §12.8)
 *
 * Signs a PDF with a PKCS#7 (CMS) detached signature, added as an incremental
 * update so the original bytes — and any prior signatures — stay intact.
 *
 * This demo generates a throwaway self-signed certificate with the OpenSSL
 * extension.  In production, load your real certificate and private key.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Papier\PdfDocument;
use Papier\Elements\Text;
use Papier\Signature\PdfSigner;

$outDir = __DIR__ . '/output';
@mkdir($outDir, 0777, true);

// ── 1. A document to sign ─────────────────────────────────────────────────────
$doc  = PdfDocument::create();
$doc->setTitle('Agreement');
$font = $doc->addFont('Helvetica');
$page = $doc->addPage();
$page->add(Text::write('This agreement is digitally signed.')->at(72, 750)->font($font, 14));
$unsigned = $doc->toString();

// ── 2. Obtain a certificate + key (here: self-signed, for demonstration) ──────
$pkey = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
if ($pkey === false) {
    echo "OpenSSL key generation unavailable in this environment.\n";
    exit(1);
}
$csr  = openssl_csr_new(['commonName' => 'Papier Demo', 'organizationName' => 'Papier'], $pkey, ['digest_alg' => 'sha256']);
$x509 = openssl_csr_sign($csr, null, $pkey, 365, ['digest_alg' => 'sha256']);
openssl_x509_export($x509, $certPem);
openssl_pkey_export($pkey, $keyPem);

// ── 3. Sign ───────────────────────────────────────────────────────────────────
$signer = new PdfSigner($certPem, $keyPem);
$signer->setReason('I approve this agreement')
       ->setLocation('Paris')
       ->setName('Papier Demo');
$signed = $signer->sign($unsigned);

$file = "$outDir/23_signed.pdf";
file_put_contents($file, $signed);
echo "Created: 23_signed.pdf (" . number_format(strlen($signed)) . " bytes)\n";

// ── 4. Show the signature was added incrementally and is well-formed ──────────
echo "Original preserved as prefix: " . (str_starts_with($signed, $unsigned) ? 'yes' : 'no') . "\n";
echo "Revisions (%%EOF markers):   " . substr_count($signed, '%%EOF') . "\n";
preg_match('/\/ByteRange \[(\d+) (\d+) (\d+) (\d+)\]/', $signed, $br);
echo "ByteRange: [{$br[1]} {$br[2]} {$br[3]} {$br[4]}]\n";

echo "\nDone.\n";
