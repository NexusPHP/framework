<?php

namespace Nexus\Tests\PHPStan\Rules\Methods;

abstract class Bar implements \SessionHandlerInterface
{
    public function __construct(
        public int $a
    ) {}

    public function __direct(): void {}

    public function gc(int $max_lifetime): int|false
    {
        return $max_lifetime > 0 ? $max_lifetime : false;
    }

    public function _boo(): void {}

    public function base_band(): void {}

    abstract public function readline(int $max_lifetime): void;

    protected function baziter(int $i): int
    {
        return $i;
    }
}
