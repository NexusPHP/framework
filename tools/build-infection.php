<?php

declare(strict_types=1);

/**
 * This file is part of the Nexus framework.
 *
 * (c) John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Nexus\Tools\InfectionConfigBuilder;

require __DIR__.'/vendor/autoload.php';

file_put_contents(
    __DIR__.'/../infection.json5',
    json_encode(InfectionConfigBuilder::build(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)."\n",
);
printf("\033[42;30m OK \033[0m Done!\n");
