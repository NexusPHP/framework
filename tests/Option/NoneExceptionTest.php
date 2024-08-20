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

use Nexus\Option\NoneException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(NoneException::class)]
#[Group('unit-test')]
final class NoneExceptionTest extends TestCase
{
    public function testNoneExceptionGivesCorrectMessage(): void
    {
        $noneException = new NoneException();
        self::assertSame('Attempting to unwrap a None option.', $noneException->getMessage());
    }
}
