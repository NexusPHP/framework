# Nexus Result

Nexus Result implements Rust's [Result type][5] into PHP.

Result is a type that represents either success (`Ok`) or failure (`Err`).

## Installation

    composer require nexusphp/result

## Getting Started

```php
<?php

use Nexus\Result\Err;
use Nexus\Result\Ok;
use Nexus\Result\Result;

function api_call(): Result
{
    // ...

    if ($successful) {
        return new Ok($value);
    }

    return new Err($error);
}

// call the function, then format any errors that may occur
$value = api_call()
    ->orElse(fn(string $err): Result => new Ok(sprintf('error: %s', $err)))
    ->unwrap();

```

## License

Nexus Result is licensed under the [MIT License][1].

## Resources

* [Report issues][2] and [send pull requests][3] in the [main Nexus repository][4]

[1]: LICENSE
[2]: https://github.com/NexusPHP/framework/issues
[3]: https://github.com/NexusPHP/framework/pulls
[4]: https://github.com/NexusPHP/framework
[5]: https://doc.rust-lang.org/std/result/enum.Result.html
