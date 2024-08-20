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

namespace Nexus\Tests\PHPStan\Rules\Functions;

use Nexus\PHPStan\Rules\Functions\FunctionNamingRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 *
 * @extends RuleTestCase<FunctionNamingRule>
 */
#[CoversClass(FunctionNamingRule::class)]
#[Group('unit-test')]
final class FunctionNamingRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__.'/data/function-naming.php'], [
            [
                'Function Nexus\Tests\PHPStan\Rules\Functions\Ball() should be in lower snake case format.',
                8,
            ],
            [
                'Function Nexus\Tests\PHPStan\Rules\Functions\_align() should be in lower snake case format.',
                10,
            ],
            [
                'Parameter #1 $_bar of function Nexus\Tests\PHPStan\Rules\Functions\foo() should be in camelCase format.',
                13,
            ],
            [
                'Function deride() should be namespaced using the "Nexus\\" namespace.',
                20,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new FunctionNamingRule();
    }
}
