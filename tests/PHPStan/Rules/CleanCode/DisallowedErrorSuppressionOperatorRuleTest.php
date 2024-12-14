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

use Nexus\PHPStan\Rules\CleanCode\DisallowedErrorSuppressionOperatorRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 *
 * @extends RuleTestCase<DisallowedErrorSuppressionOperatorRule>
 */
#[CoversClass(DisallowedErrorSuppressionOperatorRule::class)]
#[Group('unit-test')]
final class DisallowedErrorSuppressionOperatorRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $tip = implode("\n", array_map(
            static fn(string $tip): string => \sprintf('• %s', $tip),
            [
                'If you need to get the result and error message, use `Silencer::box()` instead.',
                'If you need only the result, use `Silencer::suppress()` instead.',
            ],
        ));

        $this->analyse([__DIR__.'/data/disallowed-error-suppression-operator.php'], [
            [
                'Use of the error control operator to suppress errors is not allowed.',
                7,
                $tip,
            ],
            [
                'Use of the error control operator to suppress errors is not allowed.',
                10,
                $tip,
            ],
            [
                'Use of the error control operator to suppress errors is not allowed.',
                11,
                $tip,
            ],
            [
                'Use of the error control operator to suppress errors is not allowed.',
                12,
                $tip,
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new DisallowedErrorSuppressionOperatorRule();
    }
}
