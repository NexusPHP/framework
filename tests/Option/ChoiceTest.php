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

namespace Nexus\Tests\Option;

use Nexus\Option\Choice;
use Nexus\Option\None;
use Nexus\Option\Some;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function Nexus\Option\option;

/**
 * @internal
 */
#[CoversClass(Choice::class)]
#[CoversFunction('Nexus\Option\option')]
#[Group('unit-test')]
final class ChoiceTest extends TestCase
{
    public function testChoiceFrom(): void
    {
        self::assertInstanceOf(Some::class, Choice::from(2));
        self::assertInstanceOf(None::class, Choice::from(null));
        self::assertInstanceOf(Some::class, Choice::from(null, false));
    }

    public function testOptionFunction(): void
    {
        self::assertInstanceOf(Some::class, option(2));
        self::assertInstanceOf(None::class, option(null));
        self::assertInstanceOf(Some::class, option(null, false));
    }
}
