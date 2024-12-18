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

use Nexus\Password\Algorithm;
use Nexus\Password\HashException;

final readonly class Argon2idHash extends AbstractArgon2Hash
{
    /**
     * @param array{
     *  memory_cost?: int,
     *  time_cost?: int,
     *  threads?: int,
     * } $options
     *
     * @throws HashException
     */
    public function __construct(Algorithm $algorithm, array $options = [])
    {
        if (Algorithm::Argon2id !== $algorithm) {
            throw new HashException(\sprintf(
                'Algorithm expected to be Algorithm::Argon2id, Algorithm::%s given.',
                $algorithm->name,
            ));
        }

        parent::__construct($algorithm, $options);
    }

    public function valid(): bool
    {
        return \defined('PASSWORD_ARGON2ID');
    }
}
