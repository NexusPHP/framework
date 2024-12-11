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

namespace Nexus\Tests\PHPStan\Rules\PhpDoc;

use Nexus\PHPStan\Rules\PhpDoc\DisallowedPhpstanDocTagRule;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;

/**
 * @extends RuleTestCase<DisallowedPhpstanDocTagRule>
 *
 * @internal
 */
#[CoversClass(DisallowedPhpstanDocTagRule::class)]
#[Group('unit-test')]
final class DisallowedPhpstanDocTagRuleTest extends RuleTestCase
{
    public function testRule(): void
    {
        $this->analyse([__DIR__.'/data/disallowed-phpstan-doc-tag.php'], [
            [
                'Disallowed PHPStan-prefixed PHPDoc tag: @phpstan-template T',
                7,
                'Use the native PHPDoc instead: @template T',
            ],
            [
                'Disallowed PHPStan-prefixed PHPDoc tag: @phpstan-extends Baz<T>',
                12,
                'Use the native PHPDoc instead: @extends Baz<T>',
            ],
            [
                'Disallowed PHPStan-prefixed PHPDoc tag: @phpstan-implements \IteratorAggregate<int, T>',
                12,
                'Use the native PHPDoc instead: @implements \IteratorAggregate<int, T>',
            ],
            [
                'Disallowed PHPStan-prefixed PHPDoc tag: @phpstan-var T',
                21,
                'Use the native PHPDoc instead: @var T',
            ],
            [
                'Disallowed PHPStan-prefixed PHPDoc tag: @phpstan-param int<0, max> $baz',
                26,
                'Use the native PHPDoc instead: @param int<0, max> $baz',
            ],
            [
                'Disallowed PHPStan-prefixed PHPDoc tag: @phpstan-return T',
                26,
                'Use the native PHPDoc instead: @return T',
            ],
        ]);
    }

    protected function getRule(): Rule
    {
        return new DisallowedPhpstanDocTagRule(
            self::getContainer()->getByType(Lexer::class),
            self::getContainer()->getByType(PhpDocParser::class),
        );
    }
}
