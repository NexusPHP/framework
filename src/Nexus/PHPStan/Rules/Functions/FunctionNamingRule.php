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

namespace Nexus\PHPStan\Rules\Functions;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

/**
 * @implements Rule<Node\Stmt\Function_>
 */
final class FunctionNamingRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Stmt\Function_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if (null === $node->namespacedName) {
            throw new ShouldNotHappenException(); // @codeCoverageIgnore
        }

        $functionName = $node->namespacedName->toString();

        if (! str_starts_with($functionName, 'Nexus\\')) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Function %s() should be namespaced using the "Nexus\\" namespace.',
                    $functionName,
                ))
                    ->identifier('nexus.functionNamespace')
                    ->build(),
            ];
        }

        $basename = basename(str_replace('\\', '/', $functionName));

        if (preg_match('/^[a-z][a-z0-9_]+$/', $basename) !== 1) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Function %s() should be in lower snake case format.',
                    $functionName,
                ))
                    ->identifier('nexus.functionCasing')
                    ->build(),
            ];
        }

        $errors = [];

        foreach (array_values($node->params) as $index => $param) {
            if (! $param->var instanceof Node\Expr\Variable) {
                continue; // @codeCoverageIgnore
            }

            if (! \is_string($param->var->name)) {
                continue; // @codeCoverageIgnore
            }

            if (preg_match('/^[a-z][a-zA-Z0-9]+$/', $param->var->name) !== 1) {
                $errors[] = RuleErrorBuilder::message(\sprintf(
                    'Parameter #%d $%s of function %s() should be in camelCase format.',
                    $index + 1,
                    $param->var->name,
                    $functionName,
                ))
                    ->identifier('nexus.functionParamCasing')
                    ->line($param->getStartLine())
                    ->build()
                ;
            }
        }

        return $errors;
    }
}
