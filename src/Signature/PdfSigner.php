<?php

declare(strict_types=1);

namespace Papier\Signature;

use Papier\Objects\PdfString;
use Papier\Parser\PdfParser;

/**
 * Digital signature for PDF documents (ISO 32000-1 §12.8).
 *
 * Adds an invisible approval signature as an incremental update: a signature
 * field + widget, an AcroForm entry, a signature dictionary with a /ByteRange
 * and a PKCS#7 (CMS) detached signature in /Contents.  The original bytes are
 * preserved, so any existing content and prior signatures remain valid.
 *
 * Example:
 *
 *   $signer = new PdfSigner($certPem, $keyPem);
 *   $signer->setReason('Approved')->setLocation('Paris');
 *   file_put_contents('signed.pdf', $signer->sign(file_get_contents('in.pdf')));
 */
final class PdfSigner
{
    /** Reserved byte length for the PKCS#7 signature (hex is twice this). */
    private int $signatureCapacity = 8192;

    private string $reason   = '';
    private string $location = '';
    private string $name     = '';
    private string $contactInfo = '';
    private ?string $signDate = null;

    /** @var array<int,string> extra (chain) certificates in PEM */
    private array $extraCerts = [];

    /**
     * @param string  $certificate  Signer certificate (PEM string or file path).
     * @param string  $privateKey   Private key (PEM string or file path).
     * @param string  $password     Private-key passphrase, if any.
     */
    public function __construct(
        private readonly string $certificate,
        private readonly string $privateKey,
        private readonly string $password = '',
    ) {}

    public function setReason(string $reason): static { $this->reason = $reason; return $this; }
    public function setLocation(string $location): static { $this->location = $location; return $this; }
    public function setName(string $name): static { $this->name = $name; return $this; }
    public function setContactInfo(string $info): static { $this->contactInfo = $info; return $this; }

    /** Override the UTC signing time (defaults to now), e.g. "20260628120000". */
    public function setDate(string $yyyymmddhhmmss): static { $this->signDate = $yyyymmddhhmmss; return $this; }

    /** Provide additional chain certificates (PEM strings). */
    public function addCertificate(string $pem): static { $this->extraCerts[] = $pem; return $this; }

    /** Increase the reserved /Contents capacity if the CMS blob is large (e.g. with timestamps). */
    public function setSignatureCapacity(int $bytes): static { $this->signatureCapacity = $bytes; return $this; }

    /** Visible-appearance box, or null for an invisible signature. */
    private ?array $visible = null;

    /**
     * Render a visible signature box on the given page (1-based) at the given
     * rectangle, showing the signer name, reason, location, and date.
     */
    public function setVisibleAppearance(float $x, float $y, float $w, float $h, int $page = 1): static
    {
        $this->visible = ['page' => $page, 'rect' => [$x, $y, $w, $h]];
        return $this;
    }

    /**
     * Sign the given PDF and return the signed bytes.
     *
     * @param string $pdf  Original PDF byte string.
     */
    public function sign(string $pdf): string
    {
        $parser = new PdfParser($pdf);
        $parser->parse();

        $visible = null;
        if ($this->visible !== null) {
            $visible = $this->visible + ['lines' => $this->appearanceLines()];
        }

        return SignatureAppender::append(
            $parser,
            $this->buildSignatureDictRaw(),
            $this->signatureCapacity,
            fn (string $signed): string => $this->pkcs7Sign($signed),
            $visible,
        );
    }

    /** Text lines for the visible appearance. @return string[] */
    private function appearanceLines(): array
    {
        $lines = [];
        $lines[] = $this->name !== '' ? "Signed by: {$this->name}" : 'Digitally signed';
        if ($this->reason !== '')   { $lines[] = "Reason: {$this->reason}"; }
        if ($this->location !== '') { $lines[] = "Location: {$this->location}"; }
        $date = $this->signDate ?? gmdate('YmdHis');
        $lines[] = 'Date: ' . preg_replace('/^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})$/', '$1-$2-$3 $4:$5:$6 UTC', $date);
        return $lines;
    }

    /** Build the signature dictionary as a verbatim string with patchable placeholders. */
    private function buildSignatureDictRaw(): string
    {
        $contentsZeros = str_repeat('0', $this->signatureCapacity * 2);
        $date = $this->signDate ?? gmdate('YmdHis');

        $s  = "<< /Type /Sig /Filter /Adobe.PPKLite /SubFilter /adbe.pkcs7.detached\n";
        // ByteRange placeholder: four 10-digit fields (first is the real 0).
        $s .= "/ByteRange [0000000000 0000000000 0000000000 0000000000]\n";
        $s .= "/Contents <{$contentsZeros}>\n";
        $s .= '/M (D:' . $date . "Z)\n";
        if ($this->name !== '')        { $s .= '/Name '        . $this->litStr($this->name) . "\n"; }
        if ($this->reason !== '')      { $s .= '/Reason '      . $this->litStr($this->reason) . "\n"; }
        if ($this->location !== '')    { $s .= '/Location '    . $this->litStr($this->location) . "\n"; }
        if ($this->contactInfo !== '') { $s .= '/ContactInfo ' . $this->litStr($this->contactInfo) . "\n"; }
        $s .= '>>';
        return $s;
    }

    private function litStr(string $v): string
    {
        return (new PdfString($v))->toString();
    }

    /** Produce a detached PKCS#7 (CMS) signature in DER form over $data. */
    private function pkcs7Sign(string $data): string
    {
        $dir = sys_get_temp_dir();
        $in  = tempnam($dir, 'pap_sig_in_');
        $out = tempnam($dir, 'pap_sig_out_');
        if ($in === false || $out === false) {
            throw new \RuntimeException('Cannot create temporary files for signing.');
        }

        try {
            file_put_contents($in, $data);

            $cert = $this->loadPem($this->certificate);
            $key  = $this->loadPem($this->privateKey);
            $pkey = openssl_pkey_get_private($key, $this->password);
            if ($pkey === false) {
                throw new \RuntimeException('Cannot load private key: ' . openssl_error_string());
            }

            $extraFile = null;
            if (!empty($this->extraCerts)) {
                $extraFile = tempnam($dir, 'pap_sig_chain_');
                file_put_contents($extraFile, implode("\n", array_map([$this, 'loadPem'], $this->extraCerts)));
            }

            $ok = openssl_pkcs7_sign(
                $in, $out, $cert, $pkey, [],
                PKCS7_DETACHED | PKCS7_BINARY,
                $extraFile,
            );
            if ($extraFile !== null) { @unlink($extraFile); }
            if (!$ok) {
                throw new \RuntimeException('PKCS#7 signing failed: ' . openssl_error_string());
            }

            return $this->smimeToDer((string) file_get_contents($out));
        } finally {
            @unlink($in);
            @unlink($out);
        }
    }

    /** Accept either a PEM string or a path to a PEM file. */
    private function loadPem(string $certOrPath): string
    {
        if (str_contains($certOrPath, '-----BEGIN')) {
            return $certOrPath;
        }
        if (is_file($certOrPath)) {
            return (string) file_get_contents($certOrPath);
        }
        return $certOrPath;
    }

    /** Extract the DER PKCS#7 blob from openssl's S/MIME signing output. */
    private function smimeToDer(string $smime): string
    {
        if (!preg_match('/boundary="?([^";\r\n]+)"?/i', $smime, $m)) {
            throw new \RuntimeException('Unexpected S/MIME output (no boundary).');
        }
        $boundary = '--' . $m[1];
        foreach (explode($boundary, $smime) as $part) {
            // The MIME header also mentions pkcs7-signature in its protocol=
            // declaration, so require the actual base64-encoded body part.
            if (stripos($part, 'pkcs7-signature') === false
                || stripos($part, 'base64') === false) {
                continue;
            }
            // The base64 body follows the part's header (blank line).
            $sep = strpos($part, "\r\n\r\n");
            if ($sep === false) { $sep = strpos($part, "\n\n"); }
            if ($sep === false) { continue; }
            $body = substr($part, $sep);
            $b64  = preg_replace('/[^A-Za-z0-9+\/=]/', '', $body);
            $der  = base64_decode($b64, true);
            if ($der !== false && $der !== '') {
                return $der;
            }
        }
        throw new \RuntimeException('Could not extract PKCS#7 signature from S/MIME output.');
    }
}
