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

/**
 * @internal
 */
trait PackageTrait
{
    /**
     * @var list<string>
     */
    private static array $packages = [];

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
