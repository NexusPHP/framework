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

namespace Nexus\Result;

/**
 * A PHP implementation of Rust's Result enum.
 *
 * `Result<T, E>` is the type used for returning and propagating errors. It is
 * an enum with the variants, `Ok(T)`, representing success and containing a
 * value, and `Err(E)`, representing error and containing an error value.
 *
 * @template T
 * @template E
 *
 * @see https://doc.rust-lang.org/std/result/enum.Result.html
 */
interface Result
{
    /**
     * Returns `true` if the result is `Ok`.
     *
     * @phpstan-assert-if-true  Ok<T>  $this
     * @phpstan-assert-if-false Err<E> $this
     */
    public function isOk(): bool;

    /**
     * Returns `true` if the result is `Ok` and the value inside of it matches a predicate.
     *
     * @param (\Closure(T): bool) $predicate
     */
    public function isOkAnd(\Closure $predicate): bool;

    /**
     * Returns `true` if the result is `Err`.
     *
     * @phpstan-assert-if-true  Err<E> $this
     * @phpstan-assert-if-false Ok<T>  $this
     */
    public function isErr(): bool;

    /**
     * Returns `true` if the result is `Err` and the value inside of it matches a predicate.
     *
     * @param (\Closure(E): bool) $predicate
     */
    public function isErrAnd(\Closure $predicate): bool;

    /**
     * Maps a `Result<T, E>` to `Result<U, E>` by applying a function to a
     * contained `Ok` value, leaving an `Err` value untouched.
     *
     * @template U
     *
     * @param (\Closure(T): U) $predicate
     *
     * @return self<U, E>
     */
    public function map(\Closure $predicate): self;

    /**
     * Returns the provided default (if `Err`), or applies a function to the contained value
     * (if `Ok`).
     *
     * Arguments passed to `Result::mapOr()` are eagerly evaluated; if you are passing the result
     * of a method call, it is recommended to use `Result::mapOrElse()`, which is lazily
     * evaluated.
     *
     * @template U
     *
     * @param U                $default
     * @param (\Closure(T): U) $predicate
     *
     * @return U
     */
    public function mapOr(mixed $default, \Closure $predicate): mixed;

    /**
     * Maps a `Result<T, E>` to `U` by applying fallback function `$default` to a contained
     * `Err` value, or function `$predicate` to a contained `Ok` value.
     *
     * This method can be used to unpack a successful result while handling an error.
     *
     * @template U
     *
     * @param (\Closure(E): U) $default
     * @param (\Closure(T): U) $predicate
     *
     * @return U
     */
    public function mapOrElse(\Closure $default, \Closure $predicate): mixed;

    /**
     * Maps a `Result<T, E>` to `Result<T, F>` by applying a function to a contained
     * `Err` value, leaving an `Ok` value untouched.
     *
     * This method can be used to pass through a successful result while handling an error.
     *
     * @template F
     *
     * @param (\Closure(E): F) $predicate
     *
     * @return self<T, F>
     */
    public function mapErr(\Closure $predicate): self;

    /**
     * Returns the contained `Ok` value.
     *
     * Because this method may throw, its use is generally discouraged. Instead, prefer to
     * use pattern matching and handle the `Err` case explicitly, or call `Result::unwrapOr()`,
     * or `Result::unwrapOrElse()`.
     *
     * @return T
     *
     * @throws UnwrappedResultException if result is `Err`
     */
    public function unwrap(): mixed;

    /**
     * Returns the contained `Ok` value or a provided `$default`.
     *
     * Arguments passed to `Result::unwrapOr()` are eagerly evaluated; if you are passing
     * the result of a method call, it is recommended to use `Result::unwrapOrElse()`,
     * which is lazily evaluated.
     *
     * @template U
     *
     * @param U $default
     *
     * @return T|U
     */
    public function unwrapOr(mixed $default): mixed;

    /**
     * Returns the contained `Ok` value or computes it from a closure.
     *
     * @template U
     *
     * @param (\Closure(E): U) $op
     *
     * @return T|U
     */
    public function unwrapOrElse(\Closure $op): mixed;

    /**
     * Returns the contained `Err` value.
     *
     * Throws if the value is an `Ok`, with a custom exception message
     * provided by the `Ok`’s value.
     *
     * @return E
     *
     * @throws UnwrappedResultException if result is `Ok`
     */
    public function unwrapErr(): mixed;

    /**
     * Returns `$res` if the result is `Ok`, otherwise returns the `Err` value of self.
     *
     * Arguments passed to `Result::and()` are eagerly evaluated; if you are passing the
     * result of a method call, it is recommended to use `Result::andThen()`, which is
     * lazily evaluated.
     *
     * @template U
     *
     * @param self<U, E> $res
     *
     * @return self<U, E>
     */
    public function and(self $res): self;

    /**
     * Calls `$op` if the result is `Ok`, otherwise returns the `Err` value of self.
     *
     * This method can be used for control flow based on `Result` values. Often used to chain
     * fallible operations that may return `Err`.
     *
     * @template U
     *
     * @param (\Closure(T): self<U, E>) $op
     *
     * @return self<U, E>
     */
    public function andThen(\Closure $op): self;

    /**
     * Returns `$res` if the result is `Err`, otherwise returns the `Ok` value of self.
     *
     * Arguments passed to `Result::or()` are eagerly evaluated; if you are passing the
     * result of a method call, it is recommended to use `Result::orElse()`, which is
     * lazily evaluated.
     *
     * @template F
     *
     * @param self<T, F> $res
     *
     * @return self<T, F>
     */
    public function or(self $res): self;

    /**
     * Calls `$op` if the result is `Err`, otherwise returns the `Ok` value of self.
     *
     * This method can be used for control flow based on result values.
     *
     * @template F
     *
     * @param (\Closure(E): self<T, F>) $op
     *
     * @return self<T, F>
     */
    public function orElse(\Closure $op): self;
}
