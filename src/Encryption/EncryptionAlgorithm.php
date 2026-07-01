<?php

declare(strict_types=1);

namespace Papier\Encryption;

/**
 * Encryption algorithm for the standard security handler (§7.6).
 *
 * Used with {@see \Papier\PdfDocument::encrypt()}.  Prefer AES over RC4: the
 * RC4 variants exist only for compatibility with legacy readers.
 *
 * The backing value is the handler's internal algorithm code.
 */
enum EncryptionAlgorithm: int
{
    /** 40-bit RC4 (V=1, R=2). Weak — legacy readers only. */
    case Rc4_40  = 1;
    /** 128-bit RC4 (V=2, R=3). Weak — legacy readers only. */
    case Rc4_128 = 2;
    /** 128-bit AES (V=4, R=4). The recommended default. */
    case Aes_128 = 3;
    /** 256-bit AES (V=5, R=6, PDF 1.7 extension level 3). Strongest. */
    case Aes_256 = 4;
}
