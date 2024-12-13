<?php

declare(strict_types=1);

namespace Nexus\Tests\PHPStan\Rules\CleanCode;

class Foo
{
    public function assignment(string $foo): void
    {
        if ($foo === 'foo') {
            // ...
        }

        if ($foo = random_bytes(32)) {
            // ...
        } elseif ($foo = random_int(1, 10)) {
            // ...
        } elseif ($bar = &$this) {
            // ...
        }
    }

    public function assignByRef(): void
    {
        $p = new \ReflectionClass($this);
        $c = new \ReflectionClass($this);

        while ($p = $p->getParentClass()) {
            // ...
        }

        do {
            // ...
        } while ($c = $c->getParentClass());
    }

    public function arithmetic(int $a, int $b): void
    {
        if ($a += $b) { /* */ }
        elseif ($a -= $b) { /* */ }
        if ($a *= $b) { /* */ }
        elseif ($a /= $b) { /* */ }
        if ($a %= $b) { /* */ }
        elseif ($a **= $b) { /* */ }
    }

    public function bitwise(int $a, int $b): void
    {
        if ($a &= $b) { /* */ }
        if ($a |= $b) { /* */ }
        if ($a ^= $b) { /* */ }
        if ($a <<= $b) { /* */ }
        if ($a >>= $b) { /* */ }
    }

    public function others(string $a, ?string $b): void
    {
        if ($a .= $b) { /* */}
        if ($a ??= $b) { /* */}
    }
}
