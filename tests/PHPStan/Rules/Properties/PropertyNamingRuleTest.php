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

namespace Nexus\Tests\PHPStan\Rules\Properties;

use Nexus\PHPStan\Rules\Properties\PropertyNamingRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 *
 * @extends RuleTestCase<PropertyNamingRule>
 */
#[CoversClass(PropertyNamingRule::class)]
#[Group('unit-test')]
final class PropertyNamingRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__.'/data/property-naming.php'], [
            [
                'Property Nexus\Tests\PHPStan\Rules\Properties\Bar::$_axe should not start with an underscore.',
                9,
            ],
            [
                'Property Nexus\Tests\PHPStan\Rules\Properties\Bar::$basket_ should be in camelCase format.',
                10,
            ],
            [
                'Property Nexus\Tests\PHPStan\Rules\Properties\Bar::$Status should be in camelCase format.',
                11,
            ],
            [
                'Property Nexus\Tests\PHPStan\Rules\Properties\Foo::$basket_ should be in camelCase format.',
                17,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new PropertyNamingRule();
    }
}
