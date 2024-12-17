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

namespace Nexus\PHPStan\Rules\Methods;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassMethodNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<InClassMethodNode>
 */
final class MethodNamingRule implements Rule
{
    public function getNodeType(): string
    {
        return InClassMethodNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $method = $node->getOriginalNode();
        $methodName = $method->name->toString();
        $methodReflection = $node->getMethodReflection();
        $methodPrototype = $methodReflection->getPrototype();

        if (
            $methodPrototype !== $methodReflection
            && ! str_starts_with($methodPrototype->getDeclaringClass()->getDisplayName(), 'Nexus\\')
        ) {
            return [];
        }

        if (str_starts_with($methodName, '__')) {
            if ($method->isMagic()) {
                return [];
            }

            return [
                RuleErrorBuilder::message(\sprintf(
                    'Method %s::%s() should not start with double underscores.',
                    $node->getClassReflection()->getDisplayName(),
                    $methodName,
                ))
                    ->identifier('nexus.methodDoubleUnderscore')
                    ->build(),
            ];
        }

        if (str_starts_with($methodName, '_')) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Method %s::%s() should not start with an underscore.',
                    $node->getClassReflection()->getDisplayName(),
                    $methodName,
                ))
                    ->identifier('nexus.methodUnderscore')
                    ->build(),
            ];
        }

        if (preg_match('/^[a-z][a-zA-Z0-9]+$/', $methodName) !== 1) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Method %s::%s() should be written in camelCase format.',
                    $node->getClassReflection()->getDisplayName(),
                    $methodName,
                ))
                    ->identifier('nexus.methodCasing')
                    ->build(),
            ];
        }

        $errors = [];

        foreach (array_values($method->params) as $index => $param) {
            if (! $param->var instanceof Node\Expr\Variable) {
                continue; // @codeCoverageIgnore
            }

            if (! \is_string($param->var->name)) {
                continue; // @codeCoverageIgnore
            }

            if (preg_match('/^[a-z][a-zA-Z0-9]+$/', $param->var->name) !== 1) {
                $errors[] = RuleErrorBuilder::message(\sprintf(
                    'Parameter #%d $%s of %s::%s() should be in camelCase with no underscores.',
                    $index + 1,
                    $param->var->name,
                    $node->getClassReflection()->getDisplayName(),
                    $methodName,
                ))
                    ->identifier('nexus.methodParamNaming')
                    ->line($param->getStartLine())
                    ->build()
                ;
            }
        }

        return $errors;
    }
}
