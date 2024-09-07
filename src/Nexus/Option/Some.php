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
 * @template T
 *
 * @implements Option<T>
 */
final readonly class Some implements Option
{
    /**
     * @param T $value
     */
    public function __construct(
        private mixed $value,
    ) {}

    public function isSome(): bool
    {
        return true;
    }

    public function isSomeAnd(\Closure $predicate): bool
    {
        return $predicate($this->value);
    }

    public function isNone(): bool
    {
        return false;
    }

    public function unwrap(): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function unwrapOr(mixed $default): mixed
    {
        return $this->value;
    }

    /**
     * @return T
     */
    public function unwrapOrElse(\Closure $default): mixed
    {
        return $this->value;
    }

    /**
     * @template U
     *
     * @param (\Closure(T): U) $predicate
     *
     * @param-immediately-invoked-callable $predicate
     *
     * @return self<U>
     */
    public function map(\Closure $predicate): self
    {
        return new self($predicate($this->value));
    }

    /**
     * @template U
     *
     * @param U                $default
     * @param (\Closure(T): U) $predicate
     *
     * @param-immediately-invoked-callable $predicate
     *
     * @return U
     */
    public function mapOr(mixed $default, \Closure $predicate): mixed
    {
        return $predicate($this->value);
    }

    public function mapOrElse(\Closure $default, \Closure $predicate): mixed
    {
        return $predicate($this->value);
    }

    public function and(Option $other): Option
    {
        return $other;
    }

    public function andThen(\Closure $predicate): Option
    {
        return $predicate($this->value);
    }

    public function filter(\Closure $predicate): Option
    {
        return $predicate($this->value) ? clone $this : new None();
    }

    /**
     * @template S of Option
     *
     * @param S $other
     *
     * @return self<T>
     */
    public function or(Option $other): self
    {
        return clone $this;
    }

    /**
     * @template S of Option
     *
     * @param (\Closure(): S) $other
     *
     * @return self<T>
     */
    public function orElse(\Closure $other): self
    {
        return clone $this;
    }

    /**
     * @template S
     *
     * @param Option<S> $other
     *
     * @return ($other is Some<S> ? None : self<T>)
     */
    public function xor(Option $other): Option
    {
        return $other->isSome() ? new None() : clone $this;
    }

    /**
     * @return \ArrayIterator<int, T>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator([$this->value]);
    }
}
