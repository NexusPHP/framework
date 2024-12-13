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
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Node\Stmt>
 */
final class AssignExprInCondRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Stmt::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node instanceof Node\Stmt\If_) {
            return $this->processExprCond($node->cond, 'an if');
        }

        if ($node instanceof Node\Stmt\ElseIf_) {
            return $this->processExprCond($node->cond, 'an elseif');
        }

        if ($node instanceof Node\Stmt\While_) {
            return $this->processExprCond($node->cond, 'a while');
        }

        if ($node instanceof Node\Stmt\Do_) {
            return $this->processExprCond($node->cond, 'a do-while');
        }

        return [];
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function processExprCond(Node\Expr $cond, string $stmt): array
    {
        if ($cond instanceof Node\Expr\Assign) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Assignment inside %s condition is not allowed.',
                    $stmt,
                ))
                    ->identifier('nexus.assignInCond')
                    ->line($cond->getStartLine())
                    ->build(),
            ];
        }

        if ($cond instanceof Node\Expr\AssignRef) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Assignment by reference inside %s condition is not allowed.',
                    $stmt,
                ))
                    ->identifier('nexus.assignRefInCond')
                    ->line($cond->getStartLine())
                    ->build(),
            ];
        }

        if ($cond instanceof Node\Expr\AssignOp) {
            if (
                $cond instanceof Node\Expr\AssignOp\BitwiseAnd
                || $cond instanceof Node\Expr\AssignOp\BitwiseOr
                || $cond instanceof Node\Expr\AssignOp\BitwiseXor
                || $cond instanceof Node\Expr\AssignOp\ShiftLeft
                || $cond instanceof Node\Expr\AssignOp\ShiftRight
            ) {
                return [
                    RuleErrorBuilder::message(\sprintf(
                        'Bitwise assignment inside %s condition is not allowed.',
                        $stmt,
                    ))
                        ->identifier('nexus.bitwiseAssignInCond')
                        ->line($cond->getStartLine())
                        ->build(),
                ];
            }

            if ($cond instanceof Node\Expr\AssignOp\Coalesce) {
                return [
                    RuleErrorBuilder::message(\sprintf(
                        'Null-coalesce assignment inside %s condition is not allowed.',
                        $stmt,
                    ))
                        ->identifier('nexus.nullCoalesceAssignInCond')
                        ->line($cond->getStartLine())
                        ->build(),
                ];
            }

            if ($cond instanceof Node\Expr\AssignOp\Concat) {
                return [
                    RuleErrorBuilder::message(\sprintf(
                        'Concatenation assignment inside %s condition is not allowed.',
                        $stmt,
                    ))
                        ->identifier('nexus.concatenationAssignInCond')
                        ->line($cond->getStartLine())
                        ->build(),
                ];
            }

            return [
                RuleErrorBuilder::message(\sprintf(
                    'Arithmetic assignment inside %s condition is not allowed.',
                    $stmt,
                ))
                    ->identifier('nexus.arithmeticAssignInCond')
                    ->line($cond->getStartLine())
                    ->build(),
            ];
        }

        return [];
    }
}
