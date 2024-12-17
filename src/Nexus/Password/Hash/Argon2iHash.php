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

namespace Nexus\Password\Hash;

final class Argon2iHash extends AbstractArgon2Hash
{
    public function valid(): bool
    {
        return \defined('PASSWORD_ARGON2I');
    }
}
