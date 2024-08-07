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
#[Group('package-test')]
final class ComposerJsonTest extends TestCase
{
    /**
     * @var list<string>
     */
    private static array $packages = [];

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

            $contents = @file_get_contents($realpath);

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

    /**
     * @return list<string>
     */
    private static function getPackageDirectories(): array
    {
        if ([] !== self::$packages) {
            return self::$packages;
        }

        $finder = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                'src/Nexus',
                \FilesystemIterator::KEY_AS_FILENAME | \FilesystemIterator::UNIX_PATHS,
            ),
        );
        $finder->setMaxDepth(3);

        /**
         * @var \SplFileInfo $splFileInfo
         */
        foreach ($finder as $file => $splFileInfo) {
            if ('composer.json' === $file) {
                self::$packages[] = \dirname($splFileInfo->getPathname());
            }
        }

        return self::$packages;
    }

    private function getPackageName(string $package): string
    {
        static $specialCases = [
            'PHPStan' => 'phpstan-nexus',
        ];

        $package = basename($package);

        return \sprintf(
            'nexusphp/%s',
            $specialCases[$package] ?? strtolower(preg_replace(
                '/(?<!^)((?=[A-Z][^A-Z])|(?<![A-Z])(?=[A-Z]))/u',
                '_',
                $package,
            ) ?? $package),
        );
    }
}
