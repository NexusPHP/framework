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
 * Exception thrown when accessing the value of a `None` option.
 */
final class NoneException extends \UnderflowException
{
    public function __construct()
    {
        parent::__construct('Attempting to unwrap a None option.');
    }
}
