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

namespace Nexus\PHPStan\Rules\Properties;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\ClassPropertyNode;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Php\PhpPropertyReflection;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<ClassPropertyNode>
 */
final class PropertyNamingRule implements Rule
{
    public function getNodeType(): string
    {
        return ClassPropertyNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $node->getClassReflection();
        $propertyName = $node->getName();
        $propertyPrototype = $this->findPrototype($classReflection, $propertyName);

        if (
            null !== $propertyPrototype
            && ! str_starts_with($propertyPrototype->getDeclaringClass()->getDisplayName(), 'Nexus\\')
        ) {
            return [];
        }

        if (str_starts_with($propertyName, '_')) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Property %s::$%s should not start with an underscore.',
                    $classReflection->getDisplayName(),
                    $propertyName,
                ))->identifier('nexus.propertyUnderscore')->build(),
            ];
        }

        if (preg_match('/^[a-z][a-zA-Z0-9]+$/', $propertyName) !== 1) {
            return [
                RuleErrorBuilder::message(\sprintf(
                    'Property %s::$%s should be in camelCase format.',
                    $classReflection->getDisplayName(),
                    $propertyName,
                ))->identifier('nexus.propertyCasing')->build(),
            ];
        }

        return [];
    }

    private function findPrototype(ClassReflection $classReflection, string $propertyName): ?PhpPropertyReflection
    {
        $parentClass = $classReflection->getParentClass();

        if (null === $parentClass) {
            return null;
        }

        if (! $parentClass->hasNativeProperty($propertyName)) {
            return null; // @codeCoverageIgnore
        }

        $property = $parentClass->getNativeProperty($propertyName);

        if ($property->isPrivate()) {
            return null; // @codeCoverageIgnore
        }

        return $property;
    }
}
