<?php

declare(strict_types=1);

namespace Papier\Encryption;

use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfString};

/**
 * Standard security handler — decryption side (ISO 32000-1 §7.6.3, ISO 32000-2 §7.6.4).
 *
 * Authenticates a user or owner password against an /Encrypt dictionary and
 * decrypts string and stream data.  Supports:
 *   - RC4 (V=1/V=2, R=2/R=3)
 *   - AES-128 (V=4, R=4, CFM = AESV2)
 *   - AES-256 (V=5, R=6, CFM = AESV3)
 */
final class StandardDecryptor
{
    private const PASSWORD_PADDING = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08"
                                   . "\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";

    private const METHOD_RC4      = 'RC4';
    private const METHOD_AES      = 'AES';
    private const METHOD_IDENTITY = 'Identity';

    private function __construct(
        private readonly string $fileKey,
        private readonly int    $version,
        private readonly string $method,   // self::METHOD_*
    ) {}

    /**
     * Build a decryptor from an /Encrypt dictionary and a password.
     *
     * @param string $id1  First element of the file /ID (raw bytes).
     *
     * @throws \RuntimeException  If neither the user nor owner password matches.
     */
    public static function fromDictionary(PdfDictionary $dict, string $password, string $id1): self
    {
        $filter = $dict->get('Filter');
        if ($filter instanceof PdfName && $filter->getValue() !== 'Standard') {
            throw new \RuntimeException("Unsupported security handler: {$filter->getValue()} (only Standard is supported).");
        }

        $v = self::intOf($dict->get('V'), 0);
        $r = self::intOf($dict->get('R'), 0);
        $o = self::strOf($dict->get('O'));
        $u = self::strOf($dict->get('U'));
        $p = self::intOf($dict->get('P'), 0);
        $length = self::intOf($dict->get('Length'), 40);

        $encryptMetadata = true;
        $em = $dict->get('EncryptMetadata');
        if ($em instanceof \Papier\Objects\PdfBoolean) {
            $encryptMetadata = $em->getValue();
        }

        // Determine crypt method (RC4 vs AES) from the crypt filter for V>=4.
        $method = self::METHOD_RC4;
        if ($v >= 5) {
            $method = self::METHOD_AES;
        } elseif ($v === 4) {
            $method = self::detectV4Method($dict);
        }

        if ($v >= 5) {
            $fileKey = self::computeKeyV5($password, $o, $u, $dict);
            return new self($fileKey, $v, self::METHOD_AES);
        }

        $keyLen = ($r === 2) ? 5 : intdiv($length, 8);
        $fileKey = self::computeKeyR234($password, $o, $p, $id1, $r, $keyLen, $encryptMetadata);

        if (!self::authenticateUser($fileKey, $u, $id1, $r)) {
            // Try the supplied password as the owner password.
            $userPwd = self::recoverUserPassword($password, $o, $r, $keyLen);
            $fileKey = self::computeKeyR234($userPwd, $o, $p, $id1, $r, $keyLen, $encryptMetadata);
            if (!self::authenticateUser($fileKey, $u, $id1, $r)) {
                throw new \RuntimeException('Incorrect password for encrypted PDF.');
            }
        }

        return new self($fileKey, $v, $method);
    }

    /**
     * Decrypt a string or stream's bytes for a given object.
     */
    public function decrypt(string $data, int $objNum, int $genNum): string
    {
        if ($data === '' || $this->method === self::METHOD_IDENTITY) {
            return $data;
        }

        if ($this->version >= 5) {
            return self::aesCbcDecrypt($this->fileKey, $data);
        }

        // Per-object key (§7.6.2 Algorithm 1).
        $key = $this->fileKey
             . substr(pack('V', $objNum), 0, 3)
             . substr(pack('V', $genNum), 0, 2);
        if ($this->method === self::METHOD_AES) {
            $key .= 'sAlT';
        }
        $objKey = substr(md5($key, true), 0, min(16, strlen($this->fileKey) + 5));

        if ($this->method === self::METHOD_AES) {
            return self::aesCbcDecrypt($objKey, $data);
        }
        return self::rc4($objKey, $data);
    }

    // ── Key derivation (R2–R4) ──────────────────────────────────────────────────

    private static function computeKeyR234(
        string $password,
        string $o,
        int    $p,
        string $id1,
        int    $r,
        int    $keyLen,
        bool   $encryptMetadata,
    ): string {
        $data = self::pad($password)
              . substr($o, 0, 32)
              . pack('V', $p & 0xFFFFFFFF)
              . $id1;
        if ($r >= 4 && !$encryptMetadata) {
            $data .= "\xFF\xFF\xFF\xFF";
        }
        $key = md5($data, true);
        if ($r >= 3) {
            for ($i = 0; $i < 50; $i++) {
                $key = md5(substr($key, 0, $keyLen), true);
            }
        }
        return substr($key, 0, $keyLen);
    }

    /** §7.6.3.4 Algorithm 6 — verify a user password's derived key against /U. */
    private static function authenticateUser(string $key, string $u, string $id1, int $r): bool
    {
        if ($r === 2) {
            return self::rc4($key, self::PASSWORD_PADDING) === substr($u, 0, 32);
        }
        $hash   = md5(self::PASSWORD_PADDING . $id1, true);
        $result = self::rc4($key, $hash);
        for ($i = 1; $i < 20; $i++) {
            $k = '';
            for ($j = 0; $j < strlen($key); $j++) {
                $k .= chr(ord($key[$j]) ^ $i);
            }
            $result = self::rc4($k, $result);
        }
        return $result === substr($u, 0, 16);
    }

    /** §7.6.3.4 Algorithm 7 — recover the padded user password from /O. */
    private static function recoverUserPassword(string $ownerPwd, string $o, int $r, int $keyLen): string
    {
        $md5 = md5(self::pad($ownerPwd), true);
        if ($r >= 3) {
            for ($i = 0; $i < 50; $i++) {
                $md5 = md5(substr($md5, 0, $keyLen), true);
            }
        }
        $ownerKey = substr($md5, 0, $keyLen);

        if ($r === 2) {
            return self::rc4($ownerKey, substr($o, 0, 32));
        }
        $result = substr($o, 0, 32);
        for ($i = 19; $i >= 0; $i--) {
            $k = '';
            for ($j = 0; $j < $keyLen; $j++) {
                $k .= chr(ord($ownerKey[$j]) ^ $i);
            }
            $result = self::rc4($k, $result);
        }
        return $result;
    }

    // ── Key derivation (V5/R6, AES-256) ─────────────────────────────────────────

    private static function computeKeyV5(string $password, string $o, string $u, PdfDictionary $dict): string
    {
        $password = substr($password, 0, 127); // §7.6.4.3.3: truncate to 127 bytes
        $ue = self::strOf($dict->get('UE'));
        $oe = self::strOf($dict->get('OE'));
        $zeroIv = str_repeat("\x00", 16);

        // Try user password (Algorithm 2.A).
        $uValSalt = substr($u, 32, 8);
        $uKeySalt = substr($u, 40, 8);
        if (self::hash2B($password, $uValSalt, '') === substr($u, 0, 32)) {
            $intermediate = self::hash2B($password, $uKeySalt, '');
            return self::aesNoPadDecrypt($intermediate, $ue, $zeroIv);
        }

        // Try owner password.
        $oValSalt = substr($o, 32, 8);
        $oKeySalt = substr($o, 40, 8);
        $u48 = substr($u, 0, 48);
        if (self::hash2B($password, $oValSalt, $u48) === substr($o, 0, 32)) {
            $intermediate = self::hash2B($password, $oKeySalt, $u48);
            return self::aesNoPadDecrypt($intermediate, $oe, $zeroIv);
        }

        throw new \RuntimeException('Incorrect password for AES-256 encrypted PDF.');
    }

    /** ISO 32000-2 §7.6.4.3.4 Algorithm 2.B — iterative hash for R=6. */
    private static function hash2B(string $password, string $salt, string $u): string
    {
        $K = hash('sha256', $password . $salt . $u, true);
        $i = 0;
        do {
            $K1  = str_repeat($password . $K . $u, 64);
            $key = substr($K, 0, 16);
            $iv  = substr($K, 16, 16);
            $E   = openssl_encrypt($K1, 'AES-128-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
            $mod = 0;
            for ($j = 0; $j < 16; $j++) {
                $mod += ord($E[$j]);
            }
            $K = hash(['sha256', 'sha384', 'sha512'][$mod % 3], $E, true);
            $i++;
        } while ($i < 64 || ord($E[strlen($E) - 1]) > ($i - 32));

        return substr($K, 0, 32);
    }

    // ── Primitives ──────────────────────────────────────────────────────────────

    private static function detectV4Method(PdfDictionary $dict): string
    {
        $stmF = $dict->get('StmF');
        $cfName = $stmF instanceof PdfName ? $stmF->getValue() : 'Identity';
        if ($cfName === 'Identity') {
            return self::METHOD_IDENTITY;
        }
        $cf = $dict->get('CF');
        if ($cf instanceof PdfDictionary) {
            $filter = $cf->get($cfName);
            if ($filter instanceof PdfDictionary) {
                $cfm = $filter->get('CFM');
                if ($cfm instanceof PdfName && in_array($cfm->getValue(), ['AESV2', 'AESV3'], true)) {
                    return self::METHOD_AES;
                }
            }
        }
        return self::METHOD_RC4;
    }

    /** Decrypt AES-CBC data whose first 16 bytes are the IV (PKCS#7 padded). */
    private static function aesCbcDecrypt(string $key, string $data): string
    {
        if (strlen($data) < 16) {
            return '';
        }
        $iv     = substr($data, 0, 16);
        $cipher = substr($data, 16);
        $bits   = strlen($key) * 8;
        $plain  = openssl_decrypt($cipher, "AES-{$bits}-CBC", $key, OPENSSL_RAW_DATA, $iv);
        return $plain === false ? '' : $plain;
    }

    /** Decrypt AES-CBC with an explicit IV and no padding (for UE/OE). */
    private static function aesNoPadDecrypt(string $key, string $data, string $iv): string
    {
        $bits  = strlen($key) * 8;
        $plain = openssl_decrypt($data, "AES-{$bits}-CBC", $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        return $plain === false ? '' : $plain;
    }

    private static function rc4(string $key, string $data): string
    {
        $s   = range(0, 255);
        $j   = 0;
        $len = strlen($key);
        for ($i = 0; $i < 256; $i++) {
            $j       = ($j + $s[$i] + ord($key[$i % $len])) % 256;
            [$s[$i], $s[$j]] = [$s[$j], $s[$i]];
        }
        $i = $j = 0;
        $out = '';
        for ($k = 0; $k < strlen($data); $k++) {
            $i = ($i + 1) % 256;
            $j = ($j + $s[$i]) % 256;
            [$s[$i], $s[$j]] = [$s[$j], $s[$i]];
            $out .= chr(ord($data[$k]) ^ $s[($s[$i] + $s[$j]) % 256]);
        }
        return $out;
    }

    private static function pad(string $password): string
    {
        $pwd = substr($password, 0, 32);
        return str_pad($pwd, 32, self::PASSWORD_PADDING);
    }

    private static function intOf(?\Papier\Objects\PdfObject $o, int $default): int
    {
        return $o instanceof PdfInteger ? $o->getValue() : $default;
    }

    private static function strOf(?\Papier\Objects\PdfObject $o): string
    {
        return $o instanceof PdfString ? $o->getValue() : '';
    }
}
