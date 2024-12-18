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

namespace Nexus\Password\Hash;

use Nexus\Password\Algorithm;
use Nexus\Password\HashException;
use Nexus\Password\SaltedHashInterface;

final class Pbkdf2Hash extends AbstractHash implements SaltedHashInterface
{
    private const array ALLOWED_ALGORITHMS = [
        Algorithm::Pbkdf2HmacSha1,
        Algorithm::Pbkdf2HmacSha256,
        Algorithm::Pbkdf2HmacSha512,
    ];
    private const int DEFAULT_LENGTH = 40;
    private const int MINIMUM_LENGTH = 0;
    private const int MINIMUM_ITERATIONS = 1_000;

    /**
     * @var Algorithm::Pbkdf2HmacSha1|Algorithm::Pbkdf2HmacSha256|Algorithm::Pbkdf2HmacSha512
     */
    public readonly Algorithm $algorithm;

    /**
     * @var int<self::MINIMUM_ITERATIONS, max>
     */
    private int $iterations;

    /**
     * @var int<self::MINIMUM_LENGTH, max>
     */
    private int $length;

    /**
     * @param array{
     *  iterations?: int,
     *  length?: int,
     * } $options
     *
     * @throws HashException
     */
    public function __construct(
        Algorithm $algorithm,
        array $options = [],
    ) {
        if (! \in_array($algorithm, self::ALLOWED_ALGORITHMS, true)) {
            throw new HashException(\sprintf(
                'Algorithm expected to be any of %s, but Algorithm::%s given.',
                implode(', ', array_map(
                    static fn(Algorithm $algorithm): string => \sprintf('Algorith::%s', $algorithm->name),
                    self::ALLOWED_ALGORITHMS,
                )),
                $algorithm->name,
            ));
        }

        $this->algorithm = $algorithm;

        ['iterations' => $this->iterations, 'length' => $this->length] = $this->validatedOptions(
            $options,
            $this->defaultIterations(),
            self::DEFAULT_LENGTH,
        );
    }

    /**
     * @param array{
     *  iterations?: int,
     *  length?: int,
     * } $options
     */
    public function hash(#[\SensitiveParameter] string $password, array $options = [], string $salt = ''): string
    {
        if (! $this->isValidPassword($password)) {
            throw new HashException('Invalid password provided.');
        }

        ['iterations' => $iterations, 'length' => $length] = $this->validatedOptions(
            $options,
            $this->iterations,
            $this->length,
        );

        return bin2hex(hash_pbkdf2($this->algorithm->value, $password, $salt, $iterations, $length, true));
    }

    public function needsRehash(string $hash, array $options = []): bool
    {
        return false;
    }

    public function verify(string $password, string $hash, string $salt = ''): bool
    {
        if (! $this->isValidPassword($password)) {
            return false;
        }

        if (str_contains($hash, '$')) {
            return false;
        }

        return hash_equals($hash, $this->hash($password, salt: $salt));
    }

    public function valid(): bool
    {
        return \function_exists('hash_pbkdf2');
    }

    /**
     * @see https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html#pbkdf2
     */
    private function defaultIterations(): int
    {
        return match ($this->algorithm) {
            Algorithm::Pbkdf2HmacSha1 => 1_300_000,
            Algorithm::Pbkdf2HmacSha256 => 600_000,
            default => 210_000,
        };
    }

    /**
     * Returns validated options for `hash_pbkdf2`.
     *
     * @param array{iterations?: int, length?: int} $options
     *
     * @return array{
     *  iterations: int<self::MINIMUM_ITERATIONS, max>,
     *  length: int<self::MINIMUM_LENGTH, max>
     * }
     *
     * @throws HashException
     */
    private function validatedOptions(array $options, int $iterations, int $length): array
    {
        $iterations = $options['iterations'] ?? $iterations;
        $length = $options['length'] ?? $length;

        if ($iterations < self::MINIMUM_ITERATIONS) {
            throw new HashException(\sprintf(
                'Internal iterations expected to be %s or greater, %s given.',
                number_format(self::MINIMUM_ITERATIONS),
                number_format($iterations),
            ));
        }

        if ($length < self::MINIMUM_LENGTH) {
            throw new HashException(\sprintf(
                'Length of the output string expected to be %d or greater, %d given.',
                self::MINIMUM_LENGTH,
                $length,
            ));
        }

        return compact('iterations', 'length');
    }
}
