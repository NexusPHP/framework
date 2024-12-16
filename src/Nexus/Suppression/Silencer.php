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

namespace Nexus\Suppression;

final class Silencer
{
    /**
     * @template T
     *
     * @param (\Closure(): T) $func
     *
     * @return array{T, null|string}
     */
    public static function box(\Closure $func): array
    {
        $message = null;

        set_error_handler(static function (int $errno, string $errstr) use (&$message): bool {
            $message = $errstr;

            if (str_contains($message, '): ')) {
                $message = substr($message, (int) strpos($message, '): ') + 3);
            }

            return true;
        });

        try {
            return [$func(), $message];
        } finally {
            restore_error_handler();
        }
    }

    /**
     * @template T
     *
     * @param (\Closure(): T) $func
     *
     * @return T
     */
    public static function suppress(\Closure $func): mixed
    {
        $prevErrorLevel = error_reporting(0);

        try {
            return $func();
        } finally {
            error_reporting($prevErrorLevel);
        }
    }
}
