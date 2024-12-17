# Nexus Password

Nexus Password provides secure ways to hash passwords.

## Installation

    composer require nexusphp/password

## Getting Started

```php
<?php

use Nexus\Password\Algorithm;
use Nexus\Password\Password;

$hasher = Password::forAlgorithm(Algorithm::Bcrypt, ['cost' => 15]);
$hash = $hasher->hash('password');

var_dump($hasher->needsRehash($hash)); // bool(false)
var_dump($hasher->verify('password', $hash)); // bool(true)
var_dump($hasher->verify('passwords', $hash)); // bool(false)

```

The `Algorithm` enum provides the list of supported algorithms.

## License

Nexus Password is licensed under the [MIT License][1].

## Resources

* [Report issues][2] and [send pull requests][3] in the [main Nexus repository][4]

[1]: LICENSE
[2]: https://github.com/NexusPHP/framework/issues
[3]: https://github.com/NexusPHP/framework/pulls
[4]: https://github.com/NexusPHP/framework
