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

namespace Nexus\Password;

enum Algorithm: string
{
    case Bcrypt = PASSWORD_BCRYPT;
    case Argon2i = PASSWORD_ARGON2I;
    case Argon2id = PASSWORD_ARGON2ID;
    case Sodium = 'sodium';
}
