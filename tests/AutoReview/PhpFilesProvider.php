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

use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
trait PhpFilesProvider
{
    /**
     * @var list<class-string>
     */
    private static array $sourceClasses = [];

    /**
     * @var list<class-string<TestCase>>
     */
    private static array $testClasses = [];

    /**
     * @return list<class-string>
     */
    private static function getSourceClasses(): array
    {
        if ([] !== self::$sourceClasses) {
            return self::$sourceClasses;
        }

        $directory = realpath(__DIR__.'/../../src/Nexus');
        \assert(\is_string($directory));

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $directory,
                \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS,
            ),
        );
        $sourceClasses = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (
                ! $file->isFile()
                || $file->getExtension() !== 'php'
                || $file->getFilename() === 'functions.php'
            ) {
                continue;
            }

            $relativePath = substr_replace(
                $file->getPathname(),
                '',
                0,
                \strlen($directory) + 1,
            );
            $relativePath = substr_replace(
                $relativePath,
                '',
                \strlen($relativePath) - \strlen(\DIRECTORY_SEPARATOR.$file->getBasename()),
            );
            $sourceClass = \sprintf(
                'Nexus\\%s%s%s',
                strtr($relativePath, \DIRECTORY_SEPARATOR, '\\'),
                '' === $relativePath ? '' : '\\',
                $file->getBasename('.php'),
            );

            if (! class_exists($sourceClass)) {
                continue;
            }

            $sourceClasses[] = $sourceClass;
        }

        sort($sourceClasses);
        self::$sourceClasses = $sourceClasses;

        return $sourceClasses;
    }

    /**
     * @return list<class-string<TestCase>>
     */
    private static function getTestClasses(): array
    {
        if ([] !== self::$testClasses) {
            return self::$testClasses;
        }

        $directory = realpath(__DIR__.'/..');
        \assert(\is_string($directory));

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(
                $directory,
                \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS,
            ),
            \RecursiveIteratorIterator::CHILD_FIRST,
        );
        $testClasses = [];

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (
                ! $file->isFile()
                || $file->getExtension() !== 'php'
                || str_contains($file->getPath(), \DIRECTORY_SEPARATOR.'Fixtures')
                || str_contains($file->getPath(), \DIRECTORY_SEPARATOR.'data')
            ) {
                continue;
            }

            $relativePath = substr_replace(
                $file->getPathname(),
                '',
                0,
                \strlen($directory) + 1,
            );
            $relativePath = substr_replace(
                $relativePath,
                '',
                \strlen($relativePath) - \strlen(\DIRECTORY_SEPARATOR.$file->getBasename()),
            );
            $testClass = \sprintf(
                'Nexus\\Tests\\%s%s%s',
                strtr($relativePath, \DIRECTORY_SEPARATOR, '\\'),
                '' === $relativePath ? '' : '\\',
                $file->getBasename('.php'),
            );

            if (! is_subclass_of($testClass, TestCase::class)) {
                continue;
            }

            $testClasses[] = $testClass;
        }

        sort($testClasses);
        self::$testClasses = $testClasses;

        return $testClasses;
    }
}
