<?php

declare(strict_types=1);

namespace Nexus\Tests\PHPStan\Rules\Enums;

enum CardSuits
{
    case SpadesSuit;
    case HEARTSSUIT;
    case diamonds_suit;
    case clubs_SUIT;
}
