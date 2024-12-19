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

namespace Nexus\Api;

use ApiGen\Analyzer\Filter;
use ApiGen\Info\ClassLikeInfo;
use ApiGen\Info\MemberInfo;
use PhpParser\Node;

final class AnalyserFilter extends Filter
{
    public function filterClassLikeNode(Node\Stmt\ClassLike $node): bool
    {
        $name = $node->namespacedName?->toString();

        if (null === $name) {
            return false;
        }

        return str_starts_with($name, 'Nexus\\');
    }

    public function filterFunctionNode(Node\Stmt\Function_ $node): bool
    {
        $name = $node->namespacedName?->toString();

        if (null === $name) {
            return false;
        }

        return str_starts_with($name, 'Nexus\\');
    }

    public function filterMemberInfo(ClassLikeInfo $classLike, MemberInfo $member): bool
    {
        $name = $classLike->name->full;

        return str_starts_with($name, 'Nexus\\');
    }
}
