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

    #[DataProvider('providePackageCases')]
    public function testLicenseContainsCorrectDetails(string $package): void
    {
        $license = $package.'/LICENSE';

        self::assertFileIsReadable($license);
        self::assertFileIsWritable($license);
        self::assertStringContainsString(
            'Copyright (c) 2024 John Paul E. Balandan, CPA <paulbalandan@gmail.com>',
            (string) file_get_contents($license),
        );
    }

    #[DataProvider('providePackageCases')]
    public function testReadmeContainsCorrectDetails(string $package): void
    {
        $readme = $package.'/README.md';
        self::assertFileIsReadable($readme);
        self::assertFileIsWritable($readme);

        $contents = (string) file_get_contents($readme);
        self::assertMatchesRegularExpression(
            \sprintf('/composer require( --dev)? %s/', preg_quote($this->getPackageName($package), '/')),
            $contents,
        );
        self::assertStringContainsString(
            \sprintf('Nexus %s is licensed under the [MIT License][1].', basename($package)),
            $contents,
        );
        self::assertStringContainsString(
            <<<'TXT'
                * [Report issues][2] and [send pull requests][3] in the [main Nexus repository][4]

                [1]: LICENSE
                [2]: https://github.com/NexusPHP/framework/issues
                [3]: https://github.com/NexusPHP/framework/pulls
                [4]: https://github.com/NexusPHP/framework
                TXT,
            $contents,
        );

        foreach ([
            \sprintf('# Nexus %s', basename($package)),
            '## Installation',
            '## Getting Started',
            '## License',
            '## Resources',
        ] as $section) {
            self::assertStringContainsString($section, $contents);
        }
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function providePackageCases(): iterable
    {
        foreach (self::getPackageDirectories() as $package) {
            yield $package => [$package];
        }
    }
}
