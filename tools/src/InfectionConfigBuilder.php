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

namespace Nexus\Tools;

use Infection\Mutator\ProfileList;
use Nexus\Clock\SystemClock;
use Nexus\Collection\Collection;
use Nexus\Password\Hash\SodiumHash;

/**
 * Inspired from https://github.com/kubawerlos/php-cs-fixer-custom-fixers/blob/main/.dev-tools/src/InfectionConfigBuilder.php.
 *
 * @internal
 */
final class InfectionConfigBuilder
{
    public const array GLOBAL_IGNORE = [
        'Nexus\\Password\\Hash\\*::isValidPassword',
    ];

    /**
     * @var list<string>
     */
    public const array UNWANTED_MUTATORS = [
        'Concat',
        'DecrementInteger',
        'FunctionCallRemoval',
        'GreaterThan',
        'GreaterThanOrEqualTo',
        'IdenticalEqual', // @deprecated
        'IncrementInteger',
        'IntegerNegation',
        'LessThan',
        'LessThanOrEqualTo',
        'Minus',
        'NotIdentical',
        'NotIdenticalNotEqual', // @deprecated
        'OneZeroInteger',
        'Plus',
        'RoundingFamily',
        'SyntaxError',
    ];

    /**
     * A map of mutators with their ignores, which can be
     * an FQCN or the method name of a class.
     *
     * @var array<string, list<string>>
     */
    public const array PER_MUTATOR_IGNORE = [
        'CastBool' => [
            Collection::class.'::filterWithKey',
            Collection::class.'::reject',
        ],
        'CastInt' => [SystemClock::class],
        'CastString' => [
            Collection::class.'::toArrayKey',
        ],
        'Division' => [SystemClock::class],
        'LogicalAnd' => [SodiumHash::class.'::valid'],
        'ModEqual' => [SystemClock::class],
        'TrueValue' => [
            Collection::class.'::generateDiffHashTable',
        ],
    ];

    /**
     * @return array{
     *  '$schema': string,
     *  'source': array{
     *      'directories': list<string>,
     *      'excludes': list<string>,
     *  },
     *  'timeout': int,
     *  'logs': array<string, string|array<string, string>>,
     *  'tmpDir': string,
     *  'minMsi': int,
     *  'minCoveredMsi': int,
     *  'mutators': array<string, bool|array<string, mixed>|list<string>>,
     *  'testFramework': string,
     *  'testFrameworkOptions': string,
     * }
     */
    public static function build(): array
    {
        $config = [
            '$schema' => './tools/vendor/infection/infection/resources/schema.json',
            'source' => [
                'directories' => ['src/Nexus'],
                'excludes' => ['PHPStan'],
            ],
            'timeout' => 10,
            'logs' => [
                'text' => 'build/logs/infection/infection.log',
                'html' => 'build/logs/infection/infection.html',
                'stryker' => ['report' => '1.x'],
            ],
            'tmpDir' => 'build',
            'minMsi' => 100,
            'minCoveredMsi' => 100,
            'mutators' => ['global-ignore' => []],
            'testFramework' => 'phpunit',
            'testFrameworkOptions' => '--group=unit-test',
        ];

        $mutators = array_keys(ProfileList::ALL_MUTATORS);
        sort($mutators);

        foreach ($mutators as $mutator) {
            if (\in_array($mutator, self::UNWANTED_MUTATORS, true)) {
                continue;
            }

            if (\array_key_exists($mutator, self::PER_MUTATOR_IGNORE)) {
                $config['mutators'][$mutator] = ['ignore' => self::PER_MUTATOR_IGNORE[$mutator]];

                continue;
            }

            $config['mutators'][$mutator] = true;
        }

        $config['mutators']['global-ignore'] = self::GLOBAL_IGNORE;

        return $config;
    }
}
