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

namespace Nexus\PHPStan\Rules\Constants;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ClassConstantReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

/**
 * @implements Rule<Node\Stmt\ClassConst>
 */
final class ClassConstantNamingRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Stmt\ClassConst::class;
    }

    /**
     * @param Node\Stmt\ClassConst $node
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (! $scope->isInClass()) {
            throw new ShouldNotHappenException(); // @codeCoverageIgnore
        }

        $errors = [];

        foreach ($node->consts as $const) {
            $errors = [
                ...$errors,
                ...$this->processSingleConstant($scope->getClassReflection(), $const),
            ];
        }

        return $errors;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    private function processSingleConstant(ClassReflection $classReflection, Node\Const_ $const): array
    {
        $constantName = $const->name->toString();
        $prototype = $this->findPrototype($classReflection, $constantName);

        if (
            null !== $prototype
            && ! str_starts_with($prototype->getDeclaringClass()->getDisplayName(), 'Nexus\\')
        ) {
            return [];
        }

        if (preg_match('/^[A-Z][A-Z0-9_]+$/', $constantName) !== 1) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Constant %s::%s should be in UPPER_SNAKE_CASE format.',
                    $classReflection->getDisplayName(),
                    $constantName,
                ))
                    ->identifier('nexus.constantCasing')
                    ->line($const->getStartLine())
                    ->build(),
            ];
        }

        return [];
    }

    private function findPrototype(ClassReflection $classReflection, string $constantName): ?ClassConstantReflection
    {
        foreach ($classReflection->getImmediateInterfaces() as $immediateInterface) {
            if ($immediateInterface->hasConstant($constantName)) {
                return $immediateInterface->getConstant($constantName); // @codeCoverageIgnore
            }
        }

        $parentClass = $classReflection->getParentClass();

        if (null === $parentClass) {
            return null; // @codeCoverageIgnore
        }

        if (! $parentClass->hasConstant($constantName)) {
            return null;
        }

        $constant = $parentClass->getConstant($constantName);

        if ($constant->isPrivate()) {
            return null; // @codeCoverageIgnore
        }

        return $constant;
    }
}
