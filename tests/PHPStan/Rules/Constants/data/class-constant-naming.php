<?php

declare(strict_types=1);

namespace Nexus\Tests\PHPStan\Rules\Constants;

class Foo extends \DateTimeImmutable implements \Traversable
{
    public const ATOM = 'Y-m-d\\TH:i:sP';
    public const BAR = 1;
    public const _QUX = true;
    public const Instant = false,
        temporal = true;
}
