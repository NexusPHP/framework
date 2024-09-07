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
 * A PHP implementation of Rust's Option enum.
 *
 * @see https://doc.rust-lang.org/std/option/enum.Option.html
 *
 * @template T
 *
 * @extends \IteratorAggregate<int, T>
 */
interface Option extends \IteratorAggregate
{
    /**
     * Returns `true` if the option is a **Some** value.
     *
     * @phpstan-assert-if-true  Some<T> $this
     * @phpstan-assert-if-true  false   $this->isNone()
     * @phpstan-assert-if-false None    $this
     * @phpstan-assert-if-false true    $this->isNone()
     */
    public function isSome(): bool;

    /**
     * Returns `true` if the option is a **Some** and the value inside of it matches a predicate.
     *
     * @param (\Closure(T): bool) $predicate
     *
     * @param-immediately-invoked-callable $predicate
     */
    public function isSomeAnd(\Closure $predicate): bool;

    /**
     * Returns `true` if the option is a **None** value.
     *
     * @phpstan-assert-if-true  None    $this
     * @phpstan-assert-if-true  false   $this->isSome()
     * @phpstan-assert-if-false Some<T> $this
     * @phpstan-assert-if-false true    $this->isSome()
     */
    public function isNone(): bool;

    /**
     * Returns the contained **Some** value, consuming the `self` value.
     *
     * Because this method may throw, its use is generally discouraged.
     * Instead, prefer to call `Option::unwrapOr()` or `Option::unwrapOrElse()`.
     *
     * @return T
     *
     * @throws NoneException
     */
    public function unwrap(): mixed;

    /**
     * Returns the contained **Some** value or a provided default.
     *
     * Arguments passed to `Option::unwrapOr()` are eagerly evaluated; if you are
     * passing the result of a function call, it is recommended to use `Option::unwrapOrElse()`,
     * which is lazily evaluated.
     *
     * @template S
     *
     * @param S $default
     *
     * @return S|T
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * Returns the contained **Some** value or computes it from a closure.
     *
     * @template S
     *
     * @param (\Closure(): S) $default
     *
     * @param-immediately-invoked-callable $default
     *
     * @return S|T
     */
    public function unwrapOrElse(\Closure $default): mixed;

    /**
     * Maps an `Option<T>` to `Option<U>` by applying a function to a
     * contained value (if **Some**) or returns `None` (if **None**).
     *
     * @template U
     *
     * @param (\Closure(T): U) $predicate
     *
     * @param-immediately-invoked-callable $predicate
     *
     * @return self<U>
     */
    public function map(\Closure $predicate): self;

    /**
     * Returns the provided default result (if none), or applies a function
     * to the contained value (if any).
     *
     * Arguments passed to `Option::mapOr()` are eagerly evaluated; if you are
     * passing the result of a function call, it is recommended to use `Option::mapOrElse()`,
     * which is lazily evaluated.
     *
     * @template U
     *
     * @param U                $default
     * @param (\Closure(T): U) $predicate
     *
     * @param-immediately-invoked-callable $predicate
     *
     * @return U
     */
    public function mapOr(mixed $default, \Closure $predicate): mixed;

    /**
     * Computes a default function result (if none), or applies a different function
     * to the contained value (if any).
     *
     * @template U
     *
     * @param (\Closure(): U)  $default
     * @param (\Closure(T): U) $predicate
     *
     * @param-immediately-invoked-callable $default
     * @param-immediately-invoked-callable $predicate
     *
     * @return U
     */
    public function mapOrElse(\Closure $default, \Closure $predicate): mixed;

    /**
     * Returns **None** if the option is **None**, otherwise returns `$other`.
     *
     * Arguments passed to `Option::and()` are eagerly evaluated; if you are
     * passing the result of a function call, it is recommended to use `Option::andThen()`,
     * which is lazily evaluated.
     *
     * @template U of Option
     *
     * @param U $other
     *
     * @return U
     */
    public function and(self $other): self;

    /**
     * Returns **None** if the option is **None**, otherwise calls `$other` with the wrapped
     * value and returns the result.
     *
     * @template U of Option
     *
     * @param (\Closure(T): U) $predicate
     *
     * @param-immediately-invoked-callable $predicate
     *
     * @return U
     */
    public function andThen(\Closure $predicate): self;

    /**
     * Returns **None** if the option is **None**, otherwise calls `$predicate`
     * with the wrapped value and returns:
     * - `Some(t)` if predicate returns true (where `t` is the wrapped value), and
     * - `None` if predicate returns false.
     *
     * @param (\Closure(T): bool) $predicate
     *
     * @param-immediately-invoked-callable $predicate
     *
     * @return self<T>
     */
    public function filter(\Closure $predicate): self;

    /**
     * Returns the option if it contains a value, otherwise returns `$other`.
     *
     * Arguments passed to `Option::or()` are eagerly evaluated; if you are
     * passing the result of a function call, it is recommended to use `Option::orElse()`,
     * which is lazily evaluated.
     *
     * @template S of Option
     *
     * @param S $other
     *
     * @return S
     */
    public function or(self $other): self;

    /**
     * Returns the option if it contains a value, otherwise calls
     * `$other` and returns the result.
     *
     * @template S of Option
     *
     * @param (\Closure(): S) $other
     *
     * @param-immediately-invoked-callable $other
     *
     * @return S
     */
    public function orElse(\Closure $other): self;

    /**
     * Returns **Some** if exactly one of `self`, `$other` is **Some**, otherwise returns **None**.
     *
     * @template S
     *
     * @param self<S> $other
     *
     * @return self<S>
     */
    public function xor(self $other): self;
}
