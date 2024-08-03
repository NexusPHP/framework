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

use Nexus\CsConfig\Factory;
use Nexus\CsConfig\Fixer\Comment\NoCodeSeparatorCommentFixer;
use Nexus\CsConfig\FixerGenerator;
use Nexus\CsConfig\Ruleset\Nexus82;
use PhpCsFixer\Finder;
use PhpCsFixerCustomFixers\Fixer\MultilinePromotedPropertiesFixer;
use PhpCsFixerCustomFixers\Fixer\NoCommentedOutCodeFixer;
use PhpCsFixerCustomFixers\Fixer\NoUselessCommentFixer;
use PhpCsFixerCustomFixers\Fixer\NoUselessParenthesisFixer;
use PhpCsFixerCustomFixers\Fixer\PhpdocTypesCommaSpacesFixer;
use PhpCsFixerCustomFixers\Fixer\PhpUnitAssertArgumentsOrderFixer;
use PhpCsFixerCustomFixers\Fixer\PhpUnitNoUselessReturnFixer;
use PhpCsFixerCustomFixers\Fixer\PromotedConstructorPropertyFixer;
use PhpCsFixerCustomFixers\Fixers;

$finder = Finder::create()
    ->files()
    ->in([
        __DIR__.'/.github',
        __DIR__.'/bin',
        __DIR__.'/src',
        __DIR__.'/tests',
        __DIR__.'/tools',
    ])
    ->append([
        __FILE__,
        'bin/parallel-phpunit',
        'bin/prune-cache',
    ])
;

$overrides = [];

$options = [
    'cacheFile' => 'build/.php-cs-fixer.cache',
    'finder' => $finder,
    'customFixers' => FixerGenerator::create('tools/vendor/nexusphp/cs-config/src/Fixer', 'Nexus\\CsConfig\\Fixer')->mergeWith(new Fixers()),
    'customRules' => [
        MultilinePromotedPropertiesFixer::name() => [
            'keep_blank_lines' => false,
            'minimum_number_of_properties' => 1,
        ],
        NoCodeSeparatorCommentFixer::name() => true,
        NoCommentedOutCodeFixer::name() => true,
        NoUselessCommentFixer::name() => true,
        NoUselessParenthesisFixer::name() => true,
        PhpUnitAssertArgumentsOrderFixer::name() => true,
        PhpUnitNoUselessReturnFixer::name() => true,
        PhpdocTypesCommaSpacesFixer::name() => true,
        PromotedConstructorPropertyFixer::name() => ['promote_only_existing_properties' => false],
    ],
];

return Factory::create(new Nexus82(), $overrides, $options)->forLibrary(
    'the Nexus framework',
    'John Paul E. Balandan, CPA',
    'paulbalandan@gmail.com',
);
