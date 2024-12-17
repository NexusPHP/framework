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

use Nexus\Password\HashInterface;

abstract class AbstractHash implements HashInterface
{
    public function isValidPassword(#[\SensitiveParameter] string $password): bool
    {
        if ('' === $password || str_contains($password, "\x00")) {
            return false;
        }

        $passwordLength = \strlen($password);

        if ($passwordLength < self::MINIMUM_PASSWORD_LENGTH) {
            return false;
        }

        return $passwordLength < self::MAXIMUM_PASSWORD_LENGTH;
    }
}
