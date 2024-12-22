<?php

declare(strict_types=1);

/**
 * This file is part of the Nexus framework.
 *
 * (c) John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Nexus\Encryption;

enum Cipher: string
{
    // OpenSSL supported ciphers
    case Aes256Ctr = 'aes-256-ctr';
    case Aes256Gcm = 'aes-256-gcm';

    // Sodium supported ciphers
    case Xchacha20Poly1305 = 'xchacha20-poly1305';
    case Xsalsa20Poly1305 = 'xsalsa20-poly1305';
}
