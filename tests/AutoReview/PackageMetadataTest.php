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
final class PackageMetadataTest extends TestCase
{
    use PackageTrait;

    /**
     * @var list<string>
     */
    private const METADATA_FILES = [
        'LICENSE',
        'README.md',
    ];

    #[DataProvider('provideMetadataFilesExistForPackageCases')]
    public function testMetadataFilesExistForPackage(string $package, string $metadata): void
    {
        $metadataFile = $package.\DIRECTORY_SEPARATOR.$metadata;

        self::assertFileIsReadable($metadataFile);
        self::assertFileIsWritable($metadataFile);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function provideMetadataFilesExistForPackageCases(): iterable
    {
        foreach (self::getPackageDirectories() as $package) {
            foreach (self::METADATA_FILES as $metadata) {
                yield $package.\DIRECTORY_SEPARATOR.$metadata => [$package, $metadata];
            }
        }
    }
}
