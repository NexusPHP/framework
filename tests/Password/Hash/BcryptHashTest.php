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
        yield 'invalid' => ["pass\0word"];

        yield 'long' => [str_repeat('a', 75)];
    }

    public function testLongPasswordNearingMaxWorks(): void
    {
        $password = str_repeat('a', 72);
        $hasher = new BcryptHash(Algorithm::Bcrypt);

        $hash = $hasher->hash($password);
        self::assertTrue($hasher->verify($password, $hash));
    }

    public function testInvalidPasswordForVerify(): void
    {
        $pass1 = "abcd\0e";
        $pass2 = str_repeat('a', 75);
        $pass3 = 'password';
        $pass4 = 'pass';
        $hasher = new BcryptHash(Algorithm::Bcrypt);

        $hash = $hasher->hash($pass3);
        self::assertFalse($hasher->verify($pass1, $hash));
        self::assertFalse($hasher->verify($pass2, $hash));
        self::assertTrue($hasher->verify($pass3, $hash));
        self::assertFalse($hasher->verify($pass4, $hash));
        self::assertFalse($hasher->verify(
            $pass3,
            (new BcryptHash(Algorithm::Argon2i))->hash($pass3),
        ));
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
