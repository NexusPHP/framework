# Nexus Option

Nexus Option implements Rust's [Option type][5] into PHP.

> **Note:** Not all methods of the Option enum are implemented by this library.

## Installation

    composer require nexusphp/option

## Getting Started

The `Option` type represents an optional value: every `Option` is either
a `Some` and contains a value, or `None` and does not have a value.

```php
<?php

use Nexus\Option\None;
use Nexus\Option\Option;
use Nexus\Option\Some;

final class Container
{
    public function get(string $id): Option
    {
        $cachedObject = $this->locate($id);

        if (null === $cachedObject) {
            return new None();
        }

        return new Some($cachedObject);
    }

    // ...
}

$container = new Container();

// if 'foo' exists in the container, it will return the cached object
// otherwise, this will return the `Bar` object
$object = $container->get('foo')->unwrapOr(new Bar());

// let's say the container has the `Bar` object stored in the 'bar' key
var_dump($container->get('bar')->map(static fn(object $object): string => $object::class));
// Output: string(3) "Bar"

```

The above examples eliminates boilerplate code and unnecessary control flow structures, like:
```php

$object = $container->get('foo');

if (null === $object) {
    $object = new Bar();
}

$bar = $container->get('bar');
var_dump($bar::class);

```

This library offers a terser approach by introducing the function `Nexus\Option\option`, which
allows to set the value for the `$none` parameter which currently defaults to `null`.
```php
<?php

use function Nexus\Option\option;

// if the 1st argument is the same with the 2nd argument, which is the `$none` value,
// this will return a `None` option, otherwise a `Some` option wrapping the 1st argument
// as its value
$string = option(functionThatReturnsStringOrFalse(), false)->unwrapOr('foo');
$int = option(objectOrString(), 'bar')->unwrapOr(new Bar());

```

## License

Nexus Option is licensed under the [MIT License][1].

## Resources

* [Report issues][2] and [send pull requests][3] in the [main Nexus repository][4]

[1]: LICENSE
[2]: https://github.com/NexusPHP/framework/issues
[3]: https://github.com/NexusPHP/framework/pulls
[4]: https://github.com/NexusPHP/framework
[5]: https://doc.rust-lang.org/std/option/enum.Option.html
