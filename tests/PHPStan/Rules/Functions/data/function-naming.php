<?php

declare(strict_types=1);

namespace Nexus\Tests\PHPStan\Rules\Functions {
    function bar(): void {}

    function Ball(): void {}

    function _align(): void {}

    function foo(
        string $_bar
    ): string {
        return $_bar;
    }

    function baz(string $i): void
    {
        echo $i;
    }
}

namespace {
    function deride(): void {}
}
