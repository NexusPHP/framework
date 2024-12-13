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

namespace Nexus\Tests\PHPStan\Rules\CleanCode;

use Nexus\PHPStan\Rules\CleanCode\AssignExprInCondRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 *
 * @extends RuleTestCase<AssignExprInCondRule>
 */
#[CoversClass(AssignExprInCondRule::class)]
#[Group('unit-test')]
final class AssignExprInCondRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__.'/data/assign-expr-in-cond.php'], [
            [
                'Assignment inside an if condition is not allowed.',
                15,
            ],
            [
                'Assignment inside an elseif condition is not allowed.',
                17,
            ],
            [
                'Assignment by reference inside an elseif condition is not allowed.',
                19,
            ],
            [
                'Assignment inside a while condition is not allowed.',
                29,
            ],
            [
                'Assignment inside a do-while condition is not allowed.',
                35,
            ],
            [
                'Arithmetic assignment inside an if condition is not allowed.',
                40,
            ],
            [
                'Arithmetic assignment inside an elseif condition is not allowed.',
                41,
            ],
            [
                'Arithmetic assignment inside an if condition is not allowed.',
                42,
            ],
            [
                'Arithmetic assignment inside an elseif condition is not allowed.',
                43,
            ],
            [
                'Arithmetic assignment inside an if condition is not allowed.',
                44,
            ],
            [
                'Arithmetic assignment inside an elseif condition is not allowed.',
                45,
            ],
            [
                'Bitwise assignment inside an if condition is not allowed.',
                50,
            ],
            [
                'Bitwise assignment inside an if condition is not allowed.',
                51,
            ], [
                'Bitwise assignment inside an if condition is not allowed.',
                52,
            ], [
                'Bitwise assignment inside an if condition is not allowed.',
                53,
            ], [
                'Bitwise assignment inside an if condition is not allowed.',
                54,
            ],
            [
                'Concatenation assignment inside an if condition is not allowed.',
                59,
            ],
            [
                'Null-coalesce assignment inside an if condition is not allowed.',
                60,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new AssignExprInCondRule();
    }
}
