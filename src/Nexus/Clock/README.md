# Nexus Clock

Nexus Clock decouples applications from the system clock for better testing.

> Provides an abstraction for a [PSR-20](https://www.php-fig.org/psr/psr-20/)-compatible clock.

## Installation

    composer require nexusphp/clock

## Getting Started

```php
<?php

use Nexus\Clock\Clock;

class TimeWarp
{
    public function __construct(
        private Clock $clock
    ) {}

    public function warp(): void
    {
        $now = $this->clock->now();
        // $now is a \DateTimeImmutable object

        $this->clock->sleep(60); // Sleep for 1 minute

        var_dump($this->clock->now()->getTimestamp() - $now->getTimestamp());
    }
}

$warper = new TimeWarp(new SystemClock('UTC'));
$warper->warp(); // Outputs: int(60)

```

## License

Nexus Clock is licensed under the [MIT License][1].

## Resources

* [Report issues][2] and [send pull requests][3] in the [main Nexus repository][4]

[1]: LICENSE
[2]: https://github.com/NexusPHP/framework/issues
[3]: https://github.com/NexusPHP/framework/pulls
[4]: https://github.com/NexusPHP/framework
