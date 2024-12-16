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

use Nexus\Suppression\Silencer;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
#[Group('package-test')]
final class ComposerJsonTest extends TestCase
{
    use PackageTrait;

    public function testRootComposerJsonReplacesPackages(): void
    {
        $rootComposer = $this->getComposer(__DIR__.'/../../composer.json');

        self::assertArrayHasKey('replace', $rootComposer);
        self::assertIsArray($rootComposer['replace']);

        foreach (self::getPackageDirectories() as $directory) {
            $package = $this->getPackageName($directory);

            self::assertArrayHasKey($package, $rootComposer['replace']);
            self::assertSame('self.version', $rootComposer['replace'][$package]);
        }
    }

    #[DataProvider('providePackageHasComposerJsonCases')]
    public function testPackageHasComposerJson(string $package): void
    {
        $composerJson = \sprintf('%s/composer.json', $package);
        self::assertFileExists($composerJson);

        $composerJson = $this->getComposer($composerJson);
        self::assertArrayHasKey('minimum-stability', $composerJson);
        self::assertSame('dev', $composerJson['minimum-stability']);

        self::assertArrayHasKey('prefer-stable', $composerJson);
        self::assertTrue($composerJson['prefer-stable']);

        self::assertArrayHasKey('support', $composerJson);
        self::assertIsArray($composerJson['support']);
        self::assertArrayHasKey('issues', $composerJson['support']);
        self::assertArrayHasKey('source', $composerJson['support']);
        self::assertSame('https://github.com/NexusPHP/framework/issues', $composerJson['support']['issues']);
        self::assertSame('https://github.com/NexusPHP/framework', $composerJson['support']['source']);
    }

    #[DataProvider('providePackageHasComposerJsonCases')]
    public function testComposerJsonHasAutoloadEntries(string $package): void
    {
        $composerJson = $this->getComposer(\sprintf('%s/composer.json', $package));

        self::assertArrayHasKey('autoload', $composerJson);
        self::assertIsArray($composerJson['autoload']);
        self::assertArrayHasKey('psr-4', $composerJson['autoload']);
        self::assertIsArray($composerJson['autoload']['psr-4']);
        self::assertArrayHasKey(str_replace('/', '\\', substr($package, 4)).'\\', $composerJson['autoload']['psr-4']);

        $functionsFile = $package.\DIRECTORY_SEPARATOR.'functions.php';

        if (! is_file($functionsFile)) {
            self::assertFileDoesNotExist($package.\DIRECTORY_SEPARATOR.'function.php');

            return;
        }

        self::assertArrayHasKey('files', $composerJson['autoload']);
        self::assertIsArray($composerJson['autoload']['files']);
        self::assertContains('functions.php', $composerJson['autoload']['files']);

        $rootComposer = $this->getComposer(__DIR__.'/../../composer.json');
        self::assertArrayHasKey('autoload', $rootComposer);
        self::assertIsArray($rootComposer['autoload']);
        self::assertArrayHasKey('files', $rootComposer['autoload']);
        self::assertIsArray($rootComposer['autoload']['files']);
        self::assertContains($functionsFile, $rootComposer['autoload']['files']);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function providePackageHasComposerJsonCases(): iterable
    {
        foreach (self::getPackageDirectories() as $directory) {
            yield $directory => [$directory];
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function getComposer(string $path): array
    {
        try {
            $realpath = realpath($path);

            if (false === $realpath) {
                throw new \InvalidArgumentException(\sprintf(
                    'The composer.json at "%s" does not exist.',
                    $path,
                ));
            }

            $contents = Silencer::suppress(
                static fn(): false|string => file_get_contents($realpath),
            );

            if (false === $contents) {
                throw new \InvalidArgumentException(\sprintf(
                    'The composer.json at "%s" is not readable.',
                    substr($realpath, \strlen((string) getcwd())),
                ));
            }

            return json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\InvalidArgumentException|\JsonException $e) {
            self::fail(\sprintf('Retrieving the contents failed: %s', $e->getMessage()));
        }
    }
}
