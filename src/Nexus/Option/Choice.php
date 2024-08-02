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

namespace Nexus\Option;

/**
 * @internal
 */
final class Choice
{
    /**
     * Creates an option from the given `$value`.
     *
     * The value of a **None** option can be defined by assigning a `$none` value.
     * By default, this is equal to `null` but can be another value.
     *
     * @template T
     * @template S
     *
     * @param T $value
     * @param S $none
     *
     * @return (T is S ? None : Some<T>)
     */
    public static function from(mixed $value, mixed $none = null): Option
    {
        if ($value === $none) {
            return new None();
        }

        return new Some($value);
    }
}
