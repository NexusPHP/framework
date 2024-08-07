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

namespace Nexus\Tests\PHPStan\Rules\Methods;

use Nexus\PHPStan\Rules\Methods\MethodNamingRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 *
 * @extends RuleTestCase<MethodNamingRule>
 */
#[CoversClass(MethodNamingRule::class)]
#[Group('unit')]
final class MethodNamingRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__.'/data/method-naming.php'], [
            [
                'Method Nexus\Tests\PHPStan\Rules\Methods\Bar::__direct() should not start with double underscores.',
                11,
            ],
            [
                'Method Nexus\Tests\PHPStan\Rules\Methods\Bar::_boo() should not start with an underscore.',
                18,
            ],
            [
                'Method Nexus\Tests\PHPStan\Rules\Methods\Bar::base_band() should be written in camelCase format.',
                20,
            ],
            [
                'Parameter #1 $max_lifetime of Nexus\Tests\PHPStan\Rules\Methods\Bar::readline() should be in camelCase with no underscores.',
                22,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new MethodNamingRule();
    }
}
