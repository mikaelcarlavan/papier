<?php

declare(strict_types=1);

namespace Papier\Encryption;

use Papier\Objects\{PdfArray, PdfDictionary, PdfInteger, PdfName, PdfString};

/**
 * Standard security handler (ISO 32000-1 §7.6.3).
 *
 * Supports RC4 (40-bit / 128-bit) and AES (128-bit / 256-bit) encryption.
 *
 * Permission flags (§7.6.3.2 Table 22):
 *   bit 3  = Print (low quality)
 *   bit 4  = Modify
 *   bit 5  = Copy
 *   bit 6  = Add/modify annotations
 *   bit 9  = Fill form fields
 *   bit 10 = Extract for accessibility
 *   bit 11 = Assemble document
 *   bit 12 = Print (high quality)
 */
final class StandardSecurityHandler
{
    // Algorithms
    public const RC4_40   = 1;  // V=1, R=2, 40-bit RC4
    public const RC4_128  = 2;  // V=2, R=3, 128-bit RC4
    public const AES_128  = 3;  // V=4, R=4, 128-bit AES
    public const AES_256  = 4;  // V=5, R=6, 256-bit AES (PDF 1.7 ext)

    // Permissions (all allowed by default)
    public const PERM_PRINT      = 1 << 2;
    public const PERM_MODIFY     = 1 << 3;
    public const PERM_COPY       = 1 << 4;
    public const PERM_ANNOT      = 1 << 5;
    public const PERM_FILL_FORM  = 1 << 8;
    public const PERM_EXTRACT    = 1 << 9;
    public const PERM_ASSEMBLE   = 1 << 10;
    public const PERM_PRINT_HQ   = 1 << 11;
    public const PERM_ALL        = self::PERM_PRINT | self::PERM_MODIFY | self::PERM_COPY
                                 | self::PERM_ANNOT | self::PERM_FILL_FORM | self::PERM_EXTRACT
                                 | self::PERM_ASSEMBLE | self::PERM_PRINT_HQ;

    private string $userPassword    = '';
    private string $ownerPassword   = '';
    private int    $permissions     = self::PERM_ALL;
    private int    $algorithm       = self::AES_128;

    // Derived encryption key
    private string $encryptionKey   = '';

    public function __construct(
        string $userPassword   = '',
        string $ownerPassword  = '',
        int    $permissions    = self::PERM_ALL,
        int    $algorithm      = self::AES_128,
    ) {
        $this->userPassword  = $userPassword;
        $this->ownerPassword = $ownerPassword ?: $userPassword;
        $this->permissions   = $permissions;
        $this->algorithm     = $algorithm;
    }

    /**
     * Compute the encryption parameters and build the Encrypt dictionary.
     *
     * @param string $fileId  The first element of the file identifier (16 bytes).
     */
    public function buildEncryptDictionary(string $fileId): PdfDictionary
    {
        return match ($this->algorithm) {
            self::RC4_40  => $this->buildRC4Dict($fileId, 1, 2, 5),
            self::RC4_128 => $this->buildRC4Dict($fileId, 2, 3, 16),
            self::AES_128 => $this->buildAES128Dict($fileId),
            self::AES_256 => $this->buildAES256Dict($fileId),
            default       => throw new \InvalidArgumentException('Unknown algorithm.'),
        };
    }

    public function getEncryptionKey(): string { return $this->encryptionKey; }

    // ── RC4 ───────────────────────────────────────────────────────────────────

    private function buildRC4Dict(string $fileId, int $v, int $r, int $keyLen): PdfDictionary
    {
        $ownerHash = $this->computeOwnerHash($r, $keyLen);
        $key       = $this->computeEncryptionKey($ownerHash, $r, $keyLen, $fileId);
        $userHash  = $this->computeUserHashRC4($key, $r, $fileId);

        $this->encryptionKey = $key;

        $dict = new PdfDictionary();
        $dict->set('Filter', new PdfName('Standard'));
        $dict->set('V', new PdfInteger($v));
        $dict->set('R', new PdfInteger($r));
        $dict->set('Length', new PdfInteger($keyLen * 8));
        $dict->set('P', new PdfInteger($this->buildPValue()));
        $dict->set('O', PdfString::hex($ownerHash));
        $dict->set('U', PdfString::hex($userHash));
        return $dict;
    }

    private function computeOwnerHash(int $r, int $keyLen): string
    {
        // §7.6.3.3 Algorithm 3
        // Step 1: MD5 of the PADDED owner password (padding is required by the spec).
        $md5 = md5($this->padPassword($this->ownerPassword), true);
        if ($r >= 3) {
            for ($i = 0; $i < 50; $i++) {
                $md5 = md5(substr($md5, 0, $keyLen), true);
            }
        }
        $ownerKey = substr($md5, 0, $keyLen);

        $padded = $this->padPassword($this->userPassword);
        if ($r === 2) {
            return $this->rc4($ownerKey, $padded);
        }
        // R >= 3
        $result = $padded;
        for ($i = 0; $i < 20; $i++) {
            $k = '';
            for ($j = 0; $j < $keyLen; $j++) {
                $k .= chr(ord($ownerKey[$j]) ^ $i);
            }
            $result = $this->rc4($k, $result);
        }
        return $result;
    }

    private function computeEncryptionKey(string $ownerHash, int $r, int $keyLen, string $fileId): string
    {
        // §7.6.3.3 Algorithm 5 (R=2) / Algorithm 2 (R≥3)
        $data  = $this->padPassword($this->userPassword)
               . $ownerHash
               . pack('V', $this->buildPValue())
               . $fileId;
        // §7.6.3.3 Algorithm 2 step 6: append 0xFFFFFFFF only when metadata is NOT encrypted.
        // We always encrypt metadata, so this branch is never taken.
        $key = md5($data, true);
        if ($r >= 3) {
            for ($i = 0; $i < 50; $i++) {
                $key = md5(substr($key, 0, $keyLen), true);
            }
        }
        return substr($key, 0, $keyLen);
    }

    private function computeUserHashRC4(string $key, int $r, string $fileId): string
    {
        if ($r === 2) {
            return $this->rc4($key, self::PASSWORD_PADDING);
        }
        // R >= 3: §7.6.3.3 Algorithm 4
        $data   = md5(self::PASSWORD_PADDING . $fileId, true);
        $result = $this->rc4($key, $data);
        for ($i = 1; $i < 20; $i++) {
            $k = '';
            for ($j = 0; $j < strlen($key); $j++) {
                $k .= chr(ord($key[$j]) ^ $i);
            }
            $result = $this->rc4($k, $result);
        }
        return $result . str_repeat("\x00", 16); // pad to 32 bytes
    }

    // ── AES-128 ───────────────────────────────────────────────────────────────

    private function buildAES128Dict(string $fileId): PdfDictionary
    {
        // V=4, R=4
        $keyLen   = 16;
        $ownerHash = $this->computeOwnerHash(4, $keyLen);
        $key       = $this->computeEncryptionKey($ownerHash, 4, $keyLen, $fileId);
        $userHash  = $this->computeUserHashRC4($key, 4, $fileId);
        $this->encryptionKey = $key;

        $stmfDict = new PdfDictionary();
        $stmfDict->set('AuthEvent', new PdfName('DocOpen'));
        $stmfDict->set('CFM', new PdfName('AESV2'));
        $stmfDict->set('Length', new PdfInteger(16));

        $cfDict = new PdfDictionary();
        $cfDict->set('StdCF', $stmfDict);

        $dict = new PdfDictionary();
        $dict->set('Filter', new PdfName('Standard'));
        $dict->set('V', new PdfInteger(4));
        $dict->set('R', new PdfInteger(4));
        $dict->set('Length', new PdfInteger(128));
        $dict->set('CF', $cfDict);
        $dict->set('StmF', new PdfName('StdCF'));
        $dict->set('StrF', new PdfName('StdCF'));
        $dict->set('P', new PdfInteger($this->buildPValue()));
        $dict->set('O', PdfString::hex($ownerHash));
        $dict->set('U', PdfString::hex($userHash));
        return $dict;
    }

    // ── AES-256 ───────────────────────────────────────────────────────────────

    private function buildAES256Dict(string $fileId): PdfDictionary
    {
        // V=5, R=6 — ISO 32000-2 §7.6.4.3 (Algorithms 8, 9, 10)
        $fileKey = random_bytes(32);  // random 256-bit file encryption key

        // 8 random bytes each: validation salt and key salt for user and owner
        $uvs = random_bytes(8);  // user  validation salt
        $uks = random_bytes(8);  // user  key salt
        $ovs = random_bytes(8);  // owner validation salt
        $oks = random_bytes(8);  // owner key salt
        $zeroIv = str_repeat("\x00", 16);

        // Algorithm 8: compute U and UE
        $uHash = $this->hashAlgorithm2A($this->userPassword, $uvs);
        $U     = $uHash . $uvs . $uks;  // 48 bytes
        $ueKey = $this->hashAlgorithm2A($this->userPassword, $uks);
        $UE    = openssl_encrypt($fileKey, 'AES-256-CBC', $ueKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $zeroIv);

        // Algorithm 9: compute O and OE (U entry is part of the hash input)
        $oHash = $this->hashAlgorithm2A($this->ownerPassword, $ovs, $U);
        $O     = $oHash . $ovs . $oks;  // 48 bytes
        $oeKey = $this->hashAlgorithm2A($this->ownerPassword, $oks, $U);
        $OE    = openssl_encrypt($fileKey, 'AES-256-CBC', $oeKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $zeroIv);

        // Algorithm 10: compute Perms (AES-256-ECB, no padding, 16-byte block)
        $permsPlain = pack('V', $this->buildPValue())  // 4 bytes LE
                    . "\xFF\xFF\xFF\xFF"                // 4 bytes (upper 32 bits, required)
                    . 'T'                               // EncryptMetadata = true
                    . 'adb'                             // required literal
                    . random_bytes(4);                  // random padding
        $Perms = openssl_encrypt($permsPlain, 'AES-256-ECB', $fileKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING);

        $this->encryptionKey = $fileKey;

        $stmfDict = new PdfDictionary();
        $stmfDict->set('AuthEvent', new PdfName('DocOpen'));
        $stmfDict->set('CFM', new PdfName('AESV3'));
        $stmfDict->set('Length', new PdfInteger(32));

        $cfDict = new PdfDictionary();
        $cfDict->set('StdCF', $stmfDict);

        $dict = new PdfDictionary();
        $dict->set('Filter', new PdfName('Standard'));
        $dict->set('V', new PdfInteger(5));
        $dict->set('R', new PdfInteger(6));
        $dict->set('Length', new PdfInteger(256));
        $dict->set('CF', $cfDict);
        $dict->set('StmF', new PdfName('StdCF'));
        $dict->set('StrF', new PdfName('StdCF'));
        $dict->set('P', new PdfInteger($this->buildPValue()));
        $dict->set('O', PdfString::hex($O));
        $dict->set('U', PdfString::hex($U));
        $dict->set('OE', PdfString::hex($OE));
        $dict->set('UE', PdfString::hex($UE));
        $dict->set('Perms', PdfString::hex($Perms));
        return $dict;
    }

    /**
     * ISO 32000-2 §7.6.4.3.4 Algorithm 2.A — iterative hash for AES-256 (R=6).
     *
     * @param string $password  Raw password bytes (not padded).
     * @param string $salt      8-byte validation or key salt.
     * @param string $u         U entry (48 bytes) when computing owner hash; empty for user.
     */
    private function hashAlgorithm2A(string $password, string $salt, string $u = ''): string
    {
        $K = hash('sha256', $password . $salt . $u, true);
        $E = '';
        $i = 0;
        do {
            // K1 = (password || K || u) × 64  — length is always a multiple of 16
            $K1  = str_repeat($password . $K . $u, 64);
            $key = substr($K, 0, 16);
            $iv  = substr($K, 16, 16);
            $E   = openssl_encrypt($K1, 'AES-128-CBC', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
            // Choose next hash algorithm by sum of first 16 bytes mod 3
            $mod = 0;
            for ($j = 0; $j < 16; $j++) {
                $mod += ord($E[$j]);
            }
            $K = hash(['sha256', 'sha384', 'sha512'][$mod % 3], $E, true);
            $i++;
        } while ($i < 64 || ord($E[strlen($E) - 1]) > ($i - 32));

        return substr($K, 0, 32);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private const PASSWORD_PADDING = "\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08"
                                   . "\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";

    private function padPassword(string $password): string
    {
        $pwd = substr($password, 0, 32);
        return str_pad($pwd, 32, self::PASSWORD_PADDING);
    }

    private function buildPValue(): int
    {
        // Set required bits (1–2 are reserved) and mask to 32-bit
        $p = ($this->permissions | 0xFFFFF0C0) & 0xFFFFFFFF;
        // Convert to signed 32-bit int
        if ($p > 0x7FFFFFFF) {
            $p -= 0x100000000;
        }
        return $p;
    }

    private function rc4(string $key, string $data): string
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

    private function aesEncrypt(string $key, string $data): string
    {
        $iv = random_bytes(16);
        return $iv . openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    }

    /**
     * Encrypt stream/string data using the derived encryption key.
     *
     * @param string $data      Plaintext data.
     * @param int    $objNum    Object number (for key diversification in RC4/AES-128).
     * @param int    $genNum    Generation number.
     */
    public function encryptData(string $data, int $objNum, int $genNum): string
    {
        if ($this->encryptionKey === '') {
            throw new \LogicException('Encryption key not computed. Call buildEncryptDictionary() first.');
        }

        if ($this->algorithm === self::AES_256) {
            // AES-256 uses document key directly (no per-object diversification in V=5)
            $iv = random_bytes(16);
            return $iv . openssl_encrypt(
                $data,
                'AES-256-CBC',
                $this->encryptionKey,
                OPENSSL_RAW_DATA,
                $iv,
            );
        }

        // Per-object key diversification (§7.6.2 Algorithm 1)
        $key = $this->encryptionKey
             . substr(pack('V', $objNum), 0, 3)
             . substr(pack('V', $genNum), 0, 2);
        if ($this->algorithm === self::AES_128) {
            $key .= 'sAlT';
        }
        $objKey = substr(md5($key, true), 0, min(16, strlen($this->encryptionKey) + 5));

        if ($this->algorithm === self::AES_128) {
            $iv = random_bytes(16);
            return $iv . openssl_encrypt($data, 'AES-128-CBC', $objKey, OPENSSL_RAW_DATA, $iv);
        }
        return $this->rc4($objKey, $data);
    }
}
