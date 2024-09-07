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

use Nexus\Option\None;
use Nexus\Option\Some;
use Nexus\Tests\Option\OptionTest;
use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
#[Group('auto-review')]
final class TestCodeTest extends TestCase
{
    use PhpFilesProvider;

    private const RECOGNISED_GROUP_NAMES = [
        'auto-review',
        'package-test',
        'static-analysis',
        'unit-test',
    ];

    /**
     * @var list<class-string>
     */
    private const SOURCE_CLASSES_WITHOUT_TESTS = [];

    /**
     * @var array<class-string, list<class-string>>
     */
    private const TEST_CLASSES_COVERS = [
        OptionTest::class => [None::class, Some::class],
    ];

    /**
     * @var array<string, array{class-string<TestCase>, non-empty-string}>
     */
    private static array $dataProviderMethods = [];

    /**
     * @param class-string<TestCase> $class
     */
    #[DataProvider('provideTestClassCases')]
    public function testEachTestClassHasCorrectGroupName(string $class): void
    {
        $rc = new \ReflectionClass($class);

        if ($rc->isAbstract()) {
            $this->expectNotToPerformAssertions();

            return;
        }

        $attributes = $rc->getAttributes(Group::class);
        self::assertNotEmpty($attributes, \sprintf('Test class "%s" is missing a #[Group] attribute.', $class));

        $unrecognisedGroups = array_diff(
            array_map(static function (\ReflectionAttribute $attribute): string {
                $groupAttribute = $attribute->newInstance();
                \assert($groupAttribute instanceof Group);

                return $groupAttribute->name();
            }, $attributes),
            self::RECOGNISED_GROUP_NAMES,
        );
        self::assertEmpty($unrecognisedGroups, \sprintf(
            "Test class \"%s\" has unrecognised #[Group] attribute%s.\n%s\nExpected group names: '%s'.",
            $class,
            \count($unrecognisedGroups) > 1 ? 's' : '',
            implode("\n", array_map(
                static fn(string $group): string => \sprintf('  * #[Group(\'%s\')]', $group),
                $unrecognisedGroups,
            )),
            implode('\', \'', self::RECOGNISED_GROUP_NAMES),
        ));
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('provideEachSourceClassHasTestClassCases')]
    public function testEachSourceClassHasTestClass(string $class): void
    {
        $expectedTestClassName = \sprintf('Nexus\Tests\%sTest', substr($class, \strlen('Nexus\\')));

        if (! \array_key_exists($expectedTestClassName, self::TEST_CLASSES_COVERS)) {
            foreach (self::TEST_CLASSES_COVERS as $testClassName => $coveredClasses) {
                foreach ($coveredClasses as $coveredClass) {
                    if ($coveredClass === $class) {
                        $expectedTestClassName = $testClassName;
                        break 2;
                    }
                }
            }
        }

        if (\in_array($class, self::SOURCE_CLASSES_WITHOUT_TESTS, true)) {
            $type = (new \ReflectionClass($class))->isTrait() ? 'Trait' : 'Class';
            self::assertFalse(class_exists($expectedTestClassName), \sprintf(
                '%s "%s" already has tests, so it can be removed from %s::SOURCE_CLASSES_WITHOUT_TESTS.',
                $type,
                $class,
                self::class,
            ));
            self::markTestIncomplete(\sprintf(
                '%s "%s" has no tests yet. Please help to add them.',
                $type,
                $class,
            ));
        }

        self::assertTrue(class_exists($expectedTestClassName), \sprintf(
            'Expected test class "%s" for "%s" was not found. Please add a test.',
            $expectedTestClassName,
            $class,
        ));
    }

    /**
     * @return iterable<class-string, array{class-string}>
     */
    public static function provideEachSourceClassHasTestClassCases(): iterable
    {
        foreach (self::getSourceClasses() as $class) {
            $rc = new \ReflectionClass($class);

            if ($rc->isAbstract() || $rc->isInterface()) {
                continue;
            }

            yield $class => [$class];
        }
    }

    /**
     * @param class-string<TestCase> $testClassName
     */
    #[DataProvider('provideDataProviderMethodCases')]
    public function testDataProvidersAreCorrectlyNamed(string $testClassName, string $dataProviderMethod): void
    {
        self::assertMatchesRegularExpression('/^provide[A-Z]\S+Cases$/', $dataProviderMethod, \sprintf(
            'Data provider "%s::%s()" should start with `provide` and end with `Cases`.',
            $testClassName,
            $dataProviderMethod,
        ));
    }

    /**
     * @param class-string<TestCase> $testClassName
     */
    #[DataProvider('provideDataProviderMethodCases')]
    public function testDataProvidersDeclareCorrectReturnType(string $testClassName, string $dataProviderMethod): void
    {
        if (str_ends_with($testClassName, 'TypeInferenceTest')) {
            $this->expectNotToPerformAssertions();

            return;
        }

        $dataProvider = new \ReflectionMethod($testClassName, $dataProviderMethod);
        self::assertSame(
            'iterable',
            $dataProvider->getReturnType() instanceof \ReflectionNamedType ? $dataProvider->getReturnType()->getName() : null,
            \sprintf('Data provider "%s::%s()" must declare `iterable` as method return type.', $testClassName, $dataProviderMethod),
        );

        $docComment = $dataProvider->getDocComment();
        self::assertNotFalse($docComment, \sprintf('Data provider "%s::%s()" does not have a PHPDoc.', $testClassName, $dataProviderMethod));
        self::assertMatchesRegularExpression(
            '/@return iterable<(?:class-)?string(?:\<\S+\>)?, array\{/',
            $docComment,
            \sprintf('Return PHPDoc of data provider "%s::%s" must be an iterable of named array shape (e.g., iterable<string, array{string}>).', $testClassName, $dataProviderMethod),
        );
    }

    /**
     * @return iterable<string, array{class-string<TestCase>, non-empty-string}>
     */
    public static function provideDataProviderMethodCases(): iterable
    {
        if ([] === self::$dataProviderMethods) {
            foreach (self::getTestClasses() as $testClassName) {
                $rc = new \ReflectionClass($testClassName);

                $dataProviderMethods = array_filter(
                    $rc->getMethods(\ReflectionMethod::IS_PUBLIC),
                    static fn(\ReflectionMethod $rm): bool => $rm->isStatic()
                        && $rm->getDeclaringClass()->getName() === $rc->getName()
                        && str_starts_with($rm->getName(), 'provide'),
                );

                foreach ($dataProviderMethods as $dataProviderMethod) {
                    self::$dataProviderMethods[$testClassName.'::'.$dataProviderMethod->getName()] = [
                        $testClassName,
                        $dataProviderMethod->getName(),
                    ];
                }
            }
        }

        yield from self::$dataProviderMethods;
    }

    /**
     * @param class-string<TestCase> $class
     */
    #[DataProvider('provideTestClassCases')]
    public function testEachTestClassHasCorrectCovers(string $class): void
    {
        $rc = new \ReflectionClass($class);

        if ($rc->isAbstract()) {
            $this->expectNotToPerformAssertions();

            return;
        }

        if ($rc->getAttributes(CoversNothing::class) !== []) {
            $this->expectNotToPerformAssertions();

            return;
        }

        $functions = $rc->getAttributes(CoversFunction::class);
        $classes = $rc->getAttributes(CoversClass::class);
        $traits = $rc->getAttributes(CoversTrait::class);

        if (([] !== $functions || [] !== $traits) && [] === $classes) {
            $this->expectNotToPerformAssertions();

            return;
        }

        self::assertNotEmpty($classes, \sprintf('Test class "%s" is missing a #[CoversClass] attribute.', $class));
        $classes = array_map(static function (\ReflectionAttribute $attribute): string {
            $coversClass = $attribute->newInstance();
            \assert($coversClass instanceof CoversClass);

            return $coversClass->className();
        }, $classes);

        $expectedCovers = [];
        $parents = [];

        if (\array_key_exists($class, self::TEST_CLASSES_COVERS)) {
            $expectedCovers = [...self::TEST_CLASSES_COVERS[$class]];
        } else {
            $expectedCovers[] = str_replace('Nexus\Tests\\', 'Nexus\\', substr($class, 0, -4));
        }

        /** @var class-string $coveredClass */
        foreach ($expectedCovers as $coveredClass) {
            $parent = (new \ReflectionClass($coveredClass))->getParentClass();

            while (false !== $parent) {
                if (str_starts_with($parent->getNamespaceName(), 'Nexus\\')) {
                    $parents[] = $parent->getName();
                }

                $parent = $parent->getParentClass();
            }
        }

        $expectedCovers = [...$expectedCovers, ...$parents];
        $expectedCovers = array_unique($expectedCovers);

        $unexpectedClassCovers = array_diff($classes, $expectedCovers);
        self::assertEmpty($unexpectedClassCovers, \sprintf(
            'Test class "%s" contains unexpected #[CoversClass] attribute:'."\n%s",
            $class,
            implode("\n", array_map(
                static fn(string $name): string => \sprintf('  * #[CoversClass(\'%s\')]', $name),
                $unexpectedClassCovers,
            )),
        ));

        $incompleteClassCovers = array_diff($expectedCovers, $classes);
        self::assertEmpty($incompleteClassCovers, \sprintf(
            "Test class \"%s\" has incomplete #[CoversClass] attributes. Additionally expecting:\n%s",
            $class,
            implode("\n", array_map(
                static fn(string $name): string => \sprintf('  * #[CoversClass(\'%s\')]', $name),
                $incompleteClassCovers,
            )),
        ));
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('provideTestClassCases')]
    public function testEachTestClassIsFinalOrAbstractAndIsInternal(string $class): void
    {
        $rc = new \ReflectionClass($class);

        if ($rc->isAbstract() && ! $rc->isInterface()) {
            self::assertMatchesRegularExpression('/\AAbstract(?:\S+)TestCase\z/', $rc->getShortName(), \sprintf(
                'Abstract test case "%s" should start with `Abstract` and end with `TestCase`.',
                $class,
            ));
        } else {
            self::assertTrue($rc->isFinal(), \sprintf('Test class "%s" should be final.', $class));
        }

        $docComment = $rc->getDocComment();
        self::assertNotFalse($docComment, \sprintf('Test class "%s" is missing a class-level PHPDoc.', $class));
        self::assertStringContainsString('@internal', $docComment, \sprintf('Test class "%s" should be marked as @internal.', $class));
    }

    /**
     * @return iterable<class-string<TestCase>, array{class-string<TestCase>}>
     */
    public static function provideTestClassCases(): iterable
    {
        foreach (self::getTestClasses() as $class) {
            yield $class => [$class];
        }
    }

    #[DataProvider('provideGenericClassHasTypeInferenceTestForNamespaceCases')]
    public function testGenericClassHasTypeInferenceTestForNamespace(string $package): void
    {
        $expectedTypeInferentTest = \sprintf('Nexus\\Tests\\%1$s\\%1$sTypeInferenceTest', $package);

        self::assertTrue(class_exists($expectedTypeInferentTest), \sprintf(
            'The %s package has generic class(es) thus it requires a %s.',
            $package,
            $expectedTypeInferentTest,
        ));
        self::assertTrue(is_subclass_of($expectedTypeInferentTest, TypeInferenceTestCase::class), \sprintf(
            'Type inference test "%s" should extend %s.',
            $expectedTypeInferentTest,
            TypeInferenceTestCase::class,
        ));

        $groupAttributes = array_map(static function (\ReflectionAttribute $attribute): string {
            $groupAttribute = $attribute->newInstance();
            \assert($groupAttribute instanceof Group);

            return $groupAttribute->name();
        }, (new \ReflectionClass($expectedTypeInferentTest))->getAttributes(Group::class));
        self::assertContains('static-analysis', $groupAttributes, \sprintf(
            'Test "%s" should have the #[Group(\'static-analysis\')] attribute.',
            $expectedTypeInferentTest,
        ));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideGenericClassHasTypeInferenceTestForNamespaceCases(): iterable
    {
        $packages = [];

        foreach (self::getSourceClasses() as $class) {
            $reflection = new \ReflectionClass($class);
            $docComment = $reflection->getDocComment();

            if (false === $docComment || ! str_contains($docComment, '* @template')) {
                continue;
            }

            $package = explode('\\', $reflection->getNamespaceName())[1];

            if (\array_key_exists($package, $packages)) {
                continue;
            }

            $packages[$package] = true;

            yield $package => [$package];
        }
    }
}
