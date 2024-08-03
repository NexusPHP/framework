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

namespace Nexus\Option;

/**
 * @implements Option<never>
 */
final readonly class None implements Option
{
    public function isSome(): bool
    {
        return false;
    }

    public function isSomeAnd(\Closure $predicate): bool
    {
        return false;
    }

    public function isNone(): bool
    {
        return true;
    }

    public function unwrap(): mixed
    {
        throw new NoneException();
    }

    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    public function unwrapOrElse(\Closure $default): mixed
    {
        return $default();
    }

    public function map(\Closure $predicate): Option
    {
        return clone $this;
    }

    public function mapOr(mixed $default, \Closure $predicate): mixed
    {
        return $default;
    }

    public function mapOrElse(\Closure $default, \Closure $predicate): mixed
    {
        return $default();
    }

    public function and(Option $other): Option
    {
        return clone $this;
    }

    public function andThen(\Closure $predicate): Option
    {
        return clone $this;
    }

    public function filter(\Closure $predicate): Option
    {
        return clone $this;
    }

    public function or(Option $other): Option
    {
        return $other;
    }

    public function orElse(\Closure $other): Option
    {
        return $other();
    }

    public function xor(Option $other): Option
    {
        return $other->isSome() ? $other : clone $this;
    }

    /**
     * @return \EmptyIterator
     */
    public function getIterator(): \Traversable
    {
        return new \EmptyIterator();
    }
}