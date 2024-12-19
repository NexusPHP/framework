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

use ApiGen\Index\NamespaceIndex;
use ApiGen\Info\ClassLikeInfo;
use ApiGen\Renderer\Filter;

final class RendererFilter extends Filter
{
    public function filterNamespacePage(NamespaceIndex $namespace): bool
    {
        foreach ($namespace->children as $child) {
            if ($this->filterNamespacePage($child)) {
                return true;
            }
        }

        foreach ($namespace->class as $class) {
            if ($this->filterClassLikePage($class)) {
                return true;
            }
        }

        foreach ($namespace->interface as $interface) {
            if ($this->filterClassLikePage($interface)) {
                return true;
            }
        }

        foreach ($namespace->trait as $trait) {
            if ($this->filterClassLikePage($trait)) {
                return true;
            }
        }

        foreach ($namespace->enum as $enum) {
            if ($this->filterClassLikePage($enum)) {
                return true;
            }
        }

        foreach ($namespace->exception as $exception) {
            if ($this->filterClassLikePage($exception)) {
                return true;
            }
        }

        foreach ($namespace->function as $function) {
            if ($this->filterFunctionPage($function)) {
                return true;
            }
        }

        return false;
    }

    public function filterClassLikePage(ClassLikeInfo $classLike): bool
    {
        return $this->isClassRendered($classLike);
    }

    private function isClassRendered(ClassLikeInfo $classLike): bool
    {
        $name = $classLike->name->full;

        return str_starts_with($name, 'Nexus\\');
    }
}
