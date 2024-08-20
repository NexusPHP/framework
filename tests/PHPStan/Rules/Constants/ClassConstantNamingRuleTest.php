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

namespace Nexus\Tests\PHPStan\Rules\Constants;

use Nexus\PHPStan\Rules\Constants\ClassConstantNamingRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 *
 * @extends RuleTestCase<ClassConstantNamingRule>
 */
#[CoversClass(ClassConstantNamingRule::class)]
#[Group('unit-test')]
final class ClassConstantNamingRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__.'/data/class-constant-naming.php'], [
            [
                'Constant Nexus\Tests\PHPStan\Rules\Constants\Foo::_QUX should be in UPPER_SNAKE_CASE format.',
                11,
            ],
            [
                'Constant Nexus\Tests\PHPStan\Rules\Constants\Foo::Instant should be in UPPER_SNAKE_CASE format.',
                12,
            ],
            [
                'Constant Nexus\Tests\PHPStan\Rules\Constants\Foo::temporal should be in UPPER_SNAKE_CASE format.',
                13,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new ClassConstantNamingRule();
    }
}
