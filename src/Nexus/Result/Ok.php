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
 * @template T
 *
 * @implements Result<T, never>
 */
final readonly class Ok implements Result
{
    /**
     * @param T $value
     */
    public function __construct(
        private mixed $value,
    ) {}

    public function isOk(): bool
    {
        return true;
    }

    public function isOkAnd(\Closure $predicate): bool
    {
        return $predicate($this->value);
    }

    public function isErr(): bool
    {
        return false;
    }

    public function isErrAnd(\Closure $predicate): bool
    {
        return false;
    }

    /**
     * @template U
     *
     * @param (\Closure(T): U) $predicate
     *
     * @return self<U>
     */
    public function map(\Closure $predicate): self
    {
        return new self($predicate($this->value));
    }

    public function mapOr(mixed $default, \Closure $predicate): mixed
    {
        return $predicate($this->value);
    }

    public function mapOrElse(\Closure $default, \Closure $predicate): mixed
    {
        return $predicate($this->value);
    }

    /**
     * @return self<T>
     */
    public function mapErr(\Closure $predicate): self
    {
        return $this;
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
    public function unwrapOrElse(\Closure $op): mixed
    {
        return $this->value;
    }

    public function unwrapErr(): never
    {
        $message = static fn(string $arg): string => \sprintf('Unwrapped an Ok result: %s', $arg);

        if ($this->value instanceof \Throwable) {
            throw new UnwrappedResultException($message($this->value->getMessage()), 0, $this->value);
        }

        if (\is_scalar($this->value)) {
            throw new UnwrappedResultException($message(var_export($this->value, true)));
        }

        throw new UnwrappedResultException('Unwrapped an Ok result.');
    }

    /**
     * @template U
     * @template E
     * @template R of Result<U, E>
     *
     * @param R $res
     *
     * @return R
     */
    public function and(Result $res): Result
    {
        return $res;
    }

    /**
     * @template U
     * @template E
     * @template R of Result<U, E>
     *
     * @param (\Closure(T): R) $op
     *
     * @return R
     */
    public function andThen(\Closure $op): Result
    {
        return $op($this->value);
    }

    /**
     * @return self<T>
     */
    public function or(Result $res): self
    {
        return $this;
    }

    /**
     * @return self<T>
     */
    public function orElse(\Closure $op): self
    {
        return $this;
    }
}
