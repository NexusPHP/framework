# Nexus Suppression

Provides abstractions around error management, steering usages away from the error
suppression operator (`@`). This way we make the intent clearer (e.g., suppressing
errors vs handling them explicitly).

## Installation

    composer require nexusphp/suppression

## Getting Started

Instead of using the error suppression operator, which is considered bad practice,
you can either wrap the expression in a closure then pass it to either `Silencer::box()`
or `Silencer::suppress()`.

### `Silencer::box()`

You will typically use the `box()` method of `Silencer` when you want to execute an
error-prone operation, get its result, and get also its error message.

```php
// instead of:
$result = @mkdir('tests/Suppression');

// use:
[$result, $message] = (new Silencer())->box(fn(): bool => mkdir('tests/Suppression'));

var_dump($result); // bool(false)
var_dump($message); // string(11) "File exists"

```

The `box` method accepts a `Closure` that returns the result of the operation. The method then
return this result plus the error message, if any. If no error occurred, error message is `null`.

### `Silencer::suppress()`

You may use the `suppress()` method when you don't want the error message but just want to
retrieve the operation's result without the error, if any. The syntax is similar to `box()`:
just pass a `Closure` returning the result of the operation.

## License

Nexus Suppression is licensed under the [MIT License][1].

## Resources

* [Report issues][2] and [send pull requests][3] in the [main Nexus repository][4]

[1]: LICENSE
[2]: https://github.com/NexusPHP/framework/issues
[3]: https://github.com/NexusPHP/framework/pulls
[4]: https://github.com/NexusPHP/framework
