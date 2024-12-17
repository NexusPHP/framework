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
 * @template T
 * @template S
 *
 * @param T $value
 * @param S $none
 *
 * @return (T is S ? None : Some<T>)
 */
function option(mixed $value, mixed $none = null): Option
{
    if ($value === $none) {
        return new None();
    }

    return new Some($value);
}
