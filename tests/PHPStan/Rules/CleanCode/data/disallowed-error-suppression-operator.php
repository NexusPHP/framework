<?php

declare(strict_types=1);

namespace Nexus\Tests\PHPStan\Rules\CleanCode;

@trigger_error('Test', E_USER_WARNING);
@trigger_error('Test 2', E_USER_DEPRECATED);

$a = @$x;
@mkdir(__DIR__);
@file_get_contents(__FILE__);
