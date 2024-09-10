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

use Nexus\Tools\InfectionConfigBuilder;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversNothing]
#[Group('auto-review')]
final class InfectionConfigTest extends TestCase
{
    public function testInfectionJsonIsUpdated(): void
    {
        if (is_file(__DIR__.'/../../tools/vendor/autoload.php')) {
            require_once __DIR__.'/../../tools/vendor/autoload.php';
        } else {
            self::markTestSkipped('Install `tools` to run this test.');
        }

        $infectionJson = file_get_contents(__DIR__.'/../../infection.json5');
        self::assertIsString($infectionJson);

        $actualConfig = json_decode($infectionJson, true);
        self::assertIsArray($actualConfig);
        self::assertSame(
            InfectionConfigBuilder::build(),
            $actualConfig,
            'The infection.json5 is not updated; run `bin/build-infection` to update.',
        );
    }
}
