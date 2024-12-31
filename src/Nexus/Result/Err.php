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
 * @template E
 *
 * @implements Result<never, E>
 */
final readonly class Err implements Result
{
    /**
     * @param E $err
     */
    public function __construct(
        private mixed $err,
    ) {}

    public function isOk(): bool
    {
        return false;
    }

    public function isOkAnd(\Closure $predicate): bool
    {
        return false;
    }

    public function isErr(): bool
    {
        return true;
    }

    public function isErrAnd(\Closure $predicate): bool
    {
        return $predicate($this->err);
    }

    /**
     * @return self<E>
     */
    public function map(\Closure $predicate): self
    {
        return $this;
    }

    public function mapOr(mixed $default, \Closure $predicate): mixed
    {
        return $default;
    }

    public function mapOrElse(\Closure $default, \Closure $predicate): mixed
    {
        return $default($this->err);
    }

    /**
     * @template F
     *
     * @param (\Closure(E): F) $predicate
     *
     * @return self<F>
     */
    public function mapErr(\Closure $predicate): self
    {
        return new self($predicate($this->err));
    }

    public function unwrap(): never
    {
        $message = static fn(string $arg): string => \sprintf('Unwrapped an Err result: %s', $arg);

        if ($this->err instanceof \Throwable) {
            throw new UnwrappedResultException($message($this->err->getMessage()), 0, $this->err);
        }

        if (\is_scalar($this->err)) {
            throw new UnwrappedResultException($message(var_export($this->err, true)));
        }

        throw new UnwrappedResultException('Unwrapped an Err result.');
    }

    /**
     * @template U
     *
     * @param U $default
     *
     * @return U
     */
    public function unwrapOr(mixed $default): mixed
    {
        return $default;
    }

    /**
     * @template U
     *
     * @param (\Closure(E): U) $op
     *
     * @return U
     */
    public function unwrapOrElse(\Closure $op): mixed
    {
        return $op($this->err);
    }

    public function unwrapErr(): mixed
    {
        return $this->err;
    }

    /**
     * @return self<E>
     */
    public function and(Result $res): self
    {
        return $this;
    }

    /**
     * @return self<E>
     */
    public function andThen(\Closure $op): self
    {
        return $this;
    }

    /**
     * @template T
     * @template F
     * @template R of Result<T, F>
     *
     * @param R $res
     *
     * @return R
     */
    public function or(Result $res): Result
    {
        return $res;
    }

    /**
     * @template T
     * @template F
     * @template R of Result<T, F>
     *
     * @param (\Closure(E): R) $op
     *
     * @return R
     */
    public function orElse(\Closure $op): Result
    {
        return $op($this->err);
    }
}
