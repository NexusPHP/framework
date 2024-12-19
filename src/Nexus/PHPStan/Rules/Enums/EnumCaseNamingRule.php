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

namespace Nexus\PHPStan\Rules\Enums;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Node\Stmt\EnumCase>
 */
final class EnumCaseNamingRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Stmt\EnumCase::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $enumCaseName = $node->name->name;

        if (preg_match('/(?:[A-Z][a-z]+)+/', $enumCaseName) !== 1) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Enum case "%s" should be in PascalCase format.',
                    $enumCaseName,
                ))
                    ->identifier('nexus.enumCaseNaming')
                    ->build(),
            ];
        }

        return [];
    }
}
