<?php

declare(strict_types=1);

namespace Nexus\Tests\PHPStan\Rules\Properties;

class Bar
{
    public int $_axe = 1;
    public int $basket_ = 5;
    public string $Status = 'complete';
    public bool $finished = true;
}

class Foo
{
    public int $basket_ = 2;
}

class FooException extends \Exception
{
    /**
     * @var string
     */
    protected $message = 'a';
}
