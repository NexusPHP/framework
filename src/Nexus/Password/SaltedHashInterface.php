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

/**
 * Interface for hashers needing to pass a salt.
 */
interface SaltedHashInterface extends HashInterface
{
    /**
     * @param array{
     *  cost?: int,
     *  iterations?: int,
     *  length?: int,
     *  opslimit?: int,
     *  memlimit?: int,
     *  memory_cost?: int,
     *  threads?: int,
     *  time_cost?: int,
     * } $options
     *
     * @throws HashException
     */
    public function hash(#[\SensitiveParameter] string $password, array $options = [], string $salt = ''): string;

    public function verify(#[\SensitiveParameter] string $password, string $hash, string $salt = ''): bool;
}
