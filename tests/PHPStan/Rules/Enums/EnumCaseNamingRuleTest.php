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

namespace Nexus\Tests\PHPStan\Rules\Enums;

use Nexus\PHPStan\Rules\Enums\EnumCaseNamingRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 *
 * @extends RuleTestCase<EnumCaseNamingRule>
 */
#[CoversClass(EnumCaseNamingRule::class)]
#[Group('unit-test')]
final class EnumCaseNamingRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__.'/data/enum-case-naming.php'], [
            [
                'Enum case "HEARTSSUIT" should be in PascalCase format.',
                10,
            ],
            [
                'Enum case "diamonds_suit" should be in PascalCase format.',
                11,
            ],
            [
                'Enum case "clubs_SUIT" should be in PascalCase format.',
                12,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new EnumCaseNamingRule();
    }
}
