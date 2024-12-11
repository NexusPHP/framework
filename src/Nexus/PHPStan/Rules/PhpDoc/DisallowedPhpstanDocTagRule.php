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

namespace Nexus\PHPStan\Rules\PhpDoc;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\VirtualNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Node\Stmt>
 */
final class DisallowedPhpstanDocTagRule implements Rule
{
    /**
     * @var list<non-empty-string>
     */
    private const ALLOWED_PHPSTAN_TAGS = [
        '@phpstan-ignore',
        '@phpstan-ignore-next-line',
        '@phpstan-ignore-line',
        '@phpstan-type',
        '@phpstan-import-type',
        '@phpstan-assert',
        '@phpstan-assert-if-true',
        '@phpstan-assert-if-false',
        '@phpstan-self-out',
        '@phpstan-this-out',
        '@phpstan-allow-private-mutation',
        '@phpstan-readonly-allow-private-mutation',
        '@phpstan-require-extends',
        '@phpstan-require-implements',
    ];

    public function __construct(
        private Lexer $phpDocLexer,
        private PhpDocParser $phpDocParser,
    ) {}

    public function getNodeType(): string
    {
        return Node\Stmt::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($node instanceof VirtualNode) {
            return [];
        }

        if ($node instanceof Node\Stmt\Expression) {
            if (
                ! $node->expr instanceof Node\Expr\Assign
                && ! $node->expr instanceof Node\Expr\AssignRef
            ) {
                return [];
            }
        }

        $docComment = $node->getDocComment();

        if (null === $docComment) {
            return [];
        }

        $phpDocString = $docComment->getText();
        $tokens = new TokenIterator($this->phpDocLexer->tokenize($phpDocString));
        $phpDocNode = $this->phpDocParser->parse($tokens);
        $errors = [];

        foreach ($phpDocNode->getTags() as $phpDocTagNode) {
            $phpDocName = $phpDocTagNode->name;

            if (! str_starts_with($phpDocName, '@phpstan-')) {
                continue;
            }

            if (\in_array($phpDocName, self::ALLOWED_PHPSTAN_TAGS, true)) {
                continue;
            }

            $errors[] = RuleErrorBuilder::message(\sprintf('Disallowed PHPStan-prefixed PHPDoc tag: %s', $phpDocTagNode->__toString()))
                ->tip(\sprintf('Use the native PHPDoc instead: %s', str_replace('@phpstan-', '@', $phpDocTagNode->__toString())))
                ->line($docComment->getStartLine())
                ->identifier('nexus.phpstanPhpdocTag')
                ->build()
            ;
        }

        return $errors;
    }
}
