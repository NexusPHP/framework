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

namespace Nexus\Tests\Password\Hash;

use Nexus\Password\Algorithm;
use Nexus\Password\Hash\AbstractHash;
use Nexus\Password\Hash\BcryptHash;
use Nexus\Password\HashException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(AbstractHash::class)]
#[CoversClass(BcryptHash::class)]
#[Group('unit-test')]
final class BcryptHashTest extends TestCase
{
    #[DataProvider('provideInvalidCostProvidedThrowsOnConstructionCases')]
    public function testInvalidCostProvidedThrowsOnConstruction(int $cost): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage(\sprintf(
            'Algorithmic cost is expected to be between 4 and 31, %d given.',
            $cost,
        ));

        new BcryptHash(Algorithm::Bcrypt, compact('cost'));
    }

    /**
     * @return iterable<string, array{int}>
     */
    public static function provideInvalidCostProvidedThrowsOnConstructionCases(): iterable
    {
        yield 'less than 4' => [3];

        yield 'greater than 31' => [32];
    }

    public function testPassingCostWithinBoundsDoesNotThrow(): void
    {
        foreach (range(BcryptHash::MINIMUM_COST, BcryptHash::MAXIMUM_COST) as $cost) {
            new BcryptHash(Algorithm::Bcrypt, compact('cost'));
            $this->addToAssertionCount(1);
        }
    }

    public function testInvalidAlgorithmOnBcrypt(): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Algorithm expected to be Algorithm::Bcrypt, Algorithm::Argon2i given.');

        new BcryptHash(Algorithm::Argon2i);
    }

    public function testBasicPasswordHashing(): void
    {
        $password = 'my-awesome-password';
        $hasher = new BcryptHash(Algorithm::Bcrypt);

        self::assertTrue($hasher->valid());

        $hash = $hasher->hash($password);
        self::assertTrue($hasher->verify($password, $hash));
    }

    #[DataProvider('provideInvalidPasswordPassedToBcryptCases')]
    public function testInvalidPasswordPassedToBcrypt(string $password): void
    {
        $this->expectException(HashException::class);
        $this->expectExceptionMessage('Invalid password provided.');

        (new BcryptHash(Algorithm::Bcrypt))->hash($password);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function provideInvalidPasswordPassedToBcryptCases(): iterable
    {
        yield 'empty' => [''];

        yield 'nul' => ["pass\0word"];

        yield 'short' => ['pass'];

        yield 'long' => [str_repeat('a', 75)];

        yield 'very long' => [str_repeat('a', 4098)];
    }

    public function testLongPasswordNearingMaxWorks(): void
    {
        $password = str_repeat('a', 72);
        $hasher = new BcryptHash(Algorithm::Bcrypt);

        $hash = $hasher->hash($password);
        self::assertTrue($hasher->verify($password, $hash));
    }

    #[DataProvider('provideInvalidPasswordForVerifyCases')]
    public function testInvalidPasswordForVerify(bool $result, ?string $password, string $hash): void
    {
        $password ??= 'password';
        $hasher = new BcryptHash(Algorithm::Bcrypt);

        self::assertSame($result, $hasher->verify($password, $hash));
    }

    /**
     * @return iterable<string, array{bool, null|string, string}>
     */
    public static function provideInvalidPasswordForVerifyCases(): iterable
    {
        $hash = (new BcryptHash(Algorithm::Bcrypt))->hash('password');

        yield 'empty' => [false, '', $hash];

        yield 'nul' => [false, "pass\0word", $hash];

        yield 'short' => [false, 'aa', $hash];

        yield 'bcrypt max' => [false, str_repeat('a', 75), $hash];

        yield 'very long' => [false, str_repeat('a', 4098), $hash];

        yield 'corrupted' => [false, null, str_replace('$2y', '$3y', $hash)];

        yield 'valid hash' => [true, null, $hash];
    }

    public function testPasswordNeedsRehash(): void
    {
        $password = 'myPassword';
        $hasher = new BcryptHash(Algorithm::Bcrypt);

        $hash = $hasher->hash($password);
        self::assertFalse($hasher->needsRehash($hash));
        self::assertTrue($hasher->needsRehash($hash, ['cost' => 31]));
    }
}
