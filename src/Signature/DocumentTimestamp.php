<?php

declare(strict_types=1);

namespace Papier\Signature;

use Papier\Parser\PdfParser;

/**
 * Document timestamp (PAdES, ISO 32000-2 §12.8.5 / RFC 3161).
 *
 * Appends a `/DocTimeStamp` signature (SubFilter `/ETSI.RFC3161`) as an
 * incremental update.  The timestamp token is obtained from a Time-Stamp
 * Authority via a caller-supplied client, so no specific HTTP stack is imposed:
 *
 *   $ts = new DocumentTimestamp(function (string $digest): string {
 *       $req   = DocumentTimestamp::buildRequest($digest);     // RFC 3161 TimeStampReq (DER)
 *       $resp  = http_post('https://tsa.example/tsr', $req);   // your transport
 *       return DocumentTimestamp::extractToken($resp);          // TimeStampToken (DER)
 *   });
 *   file_put_contents('timestamped.pdf', $ts->apply($signedPdf));
 */
final class DocumentTimestamp
{
    private int $capacity = 16384;

    /** @param \Closure(string $sha256Digest): string $tsaClient returns the RFC 3161 token DER */
    public function __construct(private readonly \Closure $tsaClient) {}

    public function setCapacity(int $bytes): static { $this->capacity = $bytes; return $this; }

    /** Append a document timestamp to the given PDF bytes. */
    public function apply(string $pdf): string
    {
        $parser = new PdfParser($pdf);
        $parser->parse();

        $sigDict = "<< /Type /DocTimeStamp /Filter /Adobe.PPKLite /SubFilter /ETSI.RFC3161\n"
                 . "/ByteRange [0000000000 0000000000 0000000000 0000000000]\n"
                 . '/Contents <' . str_repeat('0', $this->capacity * 2) . ">\n>>";

        return SignatureAppender::append(
            $parser,
            $sigDict,
            $this->capacity,
            fn (string $signed): string => ($this->tsaClient)(hash('sha256', $signed, true)),
            null,
            'Timestamp1',
        );
    }

    /**
     * Build an RFC 3161 TimeStampReq (DER) for a SHA-256 message imprint.
     *
     * @param string $digest  Raw 32-byte SHA-256 digest of the signed byte range.
     */
    public static function buildRequest(string $digest, bool $certReq = true): string
    {
        // SHA-256 OID 2.16.840.1.101.3.4.2.1
        $sha256Oid = "\x06\x09\x60\x86\x48\x01\x65\x03\x04\x02\x01";
        $algId     = self::der(0x30, $sha256Oid . self::der(0x05, '')); // AlgorithmIdentifier { oid, NULL }
        $imprint   = self::der(0x30, $algId . self::der(0x04, $digest)); // MessageImprint
        $version   = self::der(0x02, "\x01");                            // INTEGER 1
        $certReqDer = $certReq ? self::der(0x01, "\xFF") : '';           // BOOLEAN TRUE
        return self::der(0x30, $version . $imprint . $certReqDer);       // TimeStampReq
    }

    /**
     * Extract the TimeStampToken (the embedded ContentInfo) from a TimeStampResp (DER).
     *
     * A TimeStampResp is SEQUENCE { status PKIStatusInfo, timeStampToken ContentInfo OPTIONAL }.
     * Returns the ContentInfo (token) DER, or the input unchanged if it already
     * looks like a bare token.
     */
    public static function extractToken(string $resp): string
    {
        if ($resp === '' || ord($resp[0]) !== 0x30) {
            return $resp;
        }
        [$content, $hdrLen] = self::readTlv($resp, 0);
        // Inside the outer SEQUENCE: first element is PKIStatusInfo (SEQUENCE),
        // the token is the following ContentInfo (SEQUENCE, tag 0x30).
        $pos = $hdrLen;
        // Skip status (a SEQUENCE).
        if ($pos < strlen($resp) && ord($resp[$pos]) === 0x30) {
            $len = self::tlvTotalLength($resp, $pos);
            $pos += $len;
        }
        if ($pos < strlen($resp) && ord($resp[$pos]) === 0x30) {
            $len = self::tlvTotalLength($resp, $pos);
            return substr($resp, $pos, $len);
        }
        return $resp;
    }

    // ── minimal DER helpers ───────────────────────────────────────────────────────

    private static function der(int $tag, string $content): string
    {
        return chr($tag) . self::derLength(strlen($content)) . $content;
    }

    private static function derLength(int $len): string
    {
        if ($len < 0x80) {
            return chr($len);
        }
        $bytes = '';
        while ($len > 0) {
            $bytes = chr($len & 0xFF) . $bytes;
            $len >>= 8;
        }
        return chr(0x80 | strlen($bytes)) . $bytes;
    }

    /** @return array{0:string,1:int} [content-start-relative payload, header length] */
    private static function readTlv(string $data, int $pos): array
    {
        $lenByte = ord($data[$pos + 1]);
        if ($lenByte < 0x80) {
            return ['', $pos + 2];
        }
        $n = $lenByte & 0x7F;
        return ['', $pos + 2 + $n];
    }

    private static function tlvTotalLength(string $data, int $pos): int
    {
        $lenByte = ord($data[$pos + 1]);
        if ($lenByte < 0x80) {
            return 2 + $lenByte;
        }
        $n = $lenByte & 0x7F;
        $len = 0;
        for ($i = 0; $i < $n; $i++) {
            $len = ($len << 8) | ord($data[$pos + 2 + $i]);
        }
        return 2 + $n + $len;
    }
}
