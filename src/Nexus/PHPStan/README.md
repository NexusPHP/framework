# PHPStan extensions for Nexus

These are Nexus-specific extensions and rules for [PHPStan](https://github.com/phpstan/phpstan).

## Installation

    composer require --dev nexusphp/phpstan-nexus

If you also install [phpstan/extension-installer](https://github.com/phpstan/extension-installer) then you're all set!

<details>
	<summary>Manual installation</summary>

If you don't want to use `phpstan/extension-installer`, include extension.neon in your project's PHPStan config:

```yml
includes:
    - vendor/nexusphp/phpstan-nexus/extension.neon
```

</details>

## Rules

The following rules will automatically be enabled once the `extension.neon` is added:

1. Class constants, properties and methods, as well as functions, should follow the correct
    naming convention:
    * Class constants - UPPER_SNAKE_CASE format
    * Class properties - camelCase format with no underscores
    * Class methods - camelCase format with no underscores, except for magic methods where double
        underscores are allowed
    * Functions - lower_snake_case format

## License

Nexus Option is licensed under the [MIT License][5].

## Resources

* [Report issues][2] and [send pull requests][3] in the [main Nexus repository][4]

[1]: https://doc.rust-lang.org/std/option/enum.Option.html
[2]: https://github.com/NexusPHP/framework/issues
[3]: https://github.com/NexusPHP/framework/pulls
[4]: https://github.com/NexusPHP/framework
[5]: LICENSE
