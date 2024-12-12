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

namespace Nexus\Tests\Collection;

use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * @internal
 */
#[CoversNothing]
#[Group('static-analysis')]
final class CollectionTypeInferenceTest extends TypeInferenceTestCase
{
    #[DataProvider('provideFileAssertsCases')]
    public function testFileAsserts(string $assertType, string $file, mixed ...$args): void
    {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }

    /**
     * @return iterable<string, list<mixed>>
     */
    public static function provideFileAssertsCases(): iterable
    {
        // @phpstan-ignore generator.valueType
        yield from self::gatherAssertTypesFromDirectory(__DIR__.'/data');
    }
}
