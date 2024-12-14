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

namespace Nexus\PHPStan\Rules\CleanCode;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\Type\Constant\ConstantIntegerType;

/**
 * @implements Rule<Node\Expr\ErrorSuppress>
 */
final class DisallowedErrorSuppressionOperatorRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Expr\ErrorSuppress::class;
    }

    /**
     * @param Node\Expr\ErrorSuppress $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (
            $node->expr instanceof Node\Expr\FuncCall
            && $node->expr->name instanceof Node\Name
            && 'trigger_error' === $node->expr->name->name
        ) {
            $arguments = $node->expr->getArgs();

            if (\count($arguments) > 1) {
                $errorType = $scope->getType($arguments[1]->value);

                if ($errorType instanceof ConstantIntegerType) {
                    $errorLevel = $errorType->getValue();

                    if (E_USER_DEPRECATED === $errorLevel) {
                        return [];
                    }
                }
            }
        }

        return [
            RuleErrorBuilder::message('Use of the error control operator to suppress errors is not allowed.')
                ->identifier('nexus.errorSuppress')
                ->addTip('If you need to get the result and error message, use `Silencer::box()` instead.')
                ->addTip('If you need only the result, use `Silencer::suppress()` instead.')
                ->build(),
        ];
    }
}
