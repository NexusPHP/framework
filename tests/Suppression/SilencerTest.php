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

namespace Nexus\Tests\Suppression;

use Nexus\Suppression\Silencer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Silencer::class)]
#[Group('unit-test')]
final class SilencerTest extends TestCase
{
    public function testSilencerBox(): void
    {
        [$result, $message] = (new Silencer())->box(
            static fn(): false|string => file_get_contents('non-existent-file.txt'),
        );
        self::assertFalse($result);
        self::assertSame('Failed to open stream: No such file or directory', $message);

        [$result, $message] = (new Silencer())->box(
            static fn(): false|string => file_get_contents(__FILE__),
        );
        self::assertIsString($result);
        self::assertNull($message);
    }

    public function testSilencerSuppress(): void
    {
        $prevErrorLevel = error_reporting();

        $result = (new Silencer())->suppress(static function (): int {
            trigger_error('Test', E_USER_WARNING);

            return 30;
        });
        self::assertSame(30, $result);

        self::assertSame($prevErrorLevel, error_reporting());
    }
}
