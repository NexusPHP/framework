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

namespace Nexus\Tests\AutoReview;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
#[Group('auto-review')]
final class SourceCodeTest extends TestCase
{
    use PhpFilesProvider;

    /**
     * Interfaces that are meant to supplement a class instead of
     * restricting what methods can be declared.
     *
     * @var list<class-string>
     */
    private const SINGLE_USE_INTERFACES = [
        \Stringable::class,
    ];

    /**
     * @param class-string $class
     */
    #[DataProvider('provideSourceClassDoesNotAbuseInterfacesCases')]
    public function testSourceClassDoesNotAbuseInterfaces(string $class): void
    {
        $rc = new \ReflectionClass($class);

        $allowedMethods = array_map(self::getPublicMethodNames(...), $rc->getInterfaces());

        if ([] !== $allowedMethods) {
            $allowedMethods = array_unique(array_merge(...array_values($allowedMethods)));
        }

        $allowedMethods = [
            ...$allowedMethods,
            '__construct',
            '__destruct',
            '__wakeup',
        ];
        $extraMethods = array_diff(
            self::getPublicMethodNames($rc),
            $allowedMethods,
        );

        sort($extraMethods);
        self::assertEmpty($extraMethods, \sprintf(
            "Class \"%s\" has public methods that are not part of its implemented interfaces.\n%s",
            $class,
            implode("\n", array_map(
                static fn(string $method): string => \sprintf('  * public function %s()', $method),
                $extraMethods,
            )),
        ));
    }

    /**
     * @return iterable<class-string, array{class-string}>
     */
    public static function provideSourceClassDoesNotAbuseInterfacesCases(): iterable
    {
        $filteredClasses = array_filter(
            self::getSourceClasses(),
            static function (string $class): bool {
                $reflection = new \ReflectionClass($class);

                $interfaces = $reflection->getInterfaceNames();
                $interfacesCount = \count($interfaces);

                if (0 === $interfacesCount) {
                    return false;
                }

                return 1 !== $interfacesCount
                    || ! \in_array($interfaces[0], self::SINGLE_USE_INTERFACES, true);
            },
        );

        foreach ($filteredClasses as $class) {
            yield $class => [$class];
        }
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('provideSourceClassCases')]
    public function testSourceClassDoesNotExposeProperties(string $class): void
    {
        $rc = new \ReflectionClass($class);

        $nonReadOnlyPublicProperties = array_filter(
            $rc->getProperties(\ReflectionProperty::IS_PUBLIC),
            static fn(\ReflectionProperty $rp): bool => ! $rp->isReadOnly(),
        );
        self::assertEmpty($nonReadOnlyPublicProperties, \sprintf(
            "Class \"%s\" has public properties which are not read-only.\n%s",
            $class,
            implode("\n", array_map(
                static fn(\ReflectionProperty $prop): string => \sprintf(
                    '  * $%s',
                    preg_replace('/^Property \[ (.+) \]$/', '$1', $prop->__toString()),
                ),
                $nonReadOnlyPublicProperties,
            )),
        ));

        $definedProtectedProps = array_map(
            static fn(\ReflectionProperty $rp): string => $rp->getName(),
            $rc->getProperties(\ReflectionProperty::IS_PROTECTED),
        );
        $allowedProtectedProps = [];
        $parent = $rc->getParentClass();

        while (false !== $parent) {
            $allowedProtectedProps = [
                ...$allowedProtectedProps,
                ...$parent->getProperties(\ReflectionProperty::IS_PROTECTED),
            ];
            $parent = $parent->getParentClass();
        }

        $allowedProtectedProps = array_map(
            static fn(\ReflectionProperty $rp): string => $rp->getName(),
            $allowedProtectedProps,
        );
        $extraProtectedProps = array_diff(
            $definedProtectedProps,
            $allowedProtectedProps,
        );

        sort($extraProtectedProps);
        self::assertEmpty($extraProtectedProps, \sprintf(
            "Class \"%s\" has protected properties not defined by its parent classes.\n%s",
            $class,
            implode("\n", array_map(
                static fn(string $prop): string => \sprintf(
                    '  * %s',
                    preg_replace(
                        '/^Property \[ (.+) \]$/',
                        '$1',
                        (new \ReflectionProperty($class, $prop))->__toString(),
                    ),
                ),
                $extraProtectedProps,
            )),
        ));
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('provideSourceClassCases')]
    public function testSourceClassDoesNotHaveUnnecessaryProtectedMethods(string $class): void
    {
        $rc = new \ReflectionClass($class);

        if ($rc->isAbstract() || $rc->isInterface()) {
            $this->expectNotToPerformAssertions();

            return;
        }

        $declaredProtectedMethods = array_map(
            static fn(\ReflectionMethod $rm): string => $rm->getName(),
            $rc->getMethods(\ReflectionMethod::IS_PROTECTED),
        );

        if ([] === $declaredProtectedMethods) {
            $this->expectNotToPerformAssertions();

            return;
        }

        $allowedProtectedMethods = [];
        $parent = $rc->getParentClass();

        while (false !== $parent) {
            $allowedProtectedMethods = [
                ...$allowedProtectedMethods,
                ...$parent->getMethods(\ReflectionMethod::IS_PROTECTED),
            ];
        }

        $allowedProtectedMethods = array_unique(array_map(
            static fn(\ReflectionMethod $rm): string => $rm->getName(),
            $allowedProtectedMethods,
        ));

        $unnecessaryProtectedMethods = array_diff($declaredProtectedMethods, $allowedProtectedMethods);
        self::assertEmpty($unnecessaryProtectedMethods, \sprintf(
            "Class \"%s\" has protected method%s not inherited from a parent class, which leads to unnecessary API maintenance. Please consider changing to private visibility.\n%s",
            $class,
            \count($unnecessaryProtectedMethods) > 1 ? 's' : '',
            implode("\n", array_map(
                static fn(string $name): string => \sprintf('  * protected function %s()', $name),
                $unnecessaryProtectedMethods,
            )),
        ));
    }

    /**
     * @return iterable<class-string, array{class-string}>
     */
    public static function provideSourceClassCases(): iterable
    {
        foreach (self::getSourceClasses() as $class) {
            yield $class => [$class];
        }
    }

    /**
     * @template T of object
     *
     * @param \ReflectionClass<T> $rc
     *
     * @return list<string>
     */
    private static function getPublicMethodNames(\ReflectionClass $rc): array
    {
        return array_map(
            static fn(\ReflectionMethod $rm): string => $rm->getName(),
            $rc->getMethods(\ReflectionMethod::IS_PUBLIC),
        );
    }
}
