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
}

namespace {
    function deride(): void {}
}
