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

if (PHP_SAPI !== 'cli') {
    echo "\033[41m FAIL \033[0m The \"bin/parallel-phpunit\" script must be run from the command line.\n";

    exit(1);
}

$exit = 0;
$args = $argv;
$coverage = false;
$runsOnGithubActions = getenv('GITHUB_ACTIONS') !== false;

if (in_array('--coverage', $args, true)) {
    $coverage = true;
    $args = array_flip($args);
    unset($args['--coverage']);
    $args = array_values(array_flip($args));
}

$directory = $args[1] ?? 'src/Nexus';
$components = [];

if (! is_dir($directory)) {
    printf("\033[41m FAIL \033[0m The \"%s\" directory does not exist.\n", $directory);

    exit(1);
}

$finder = new RecursiveDirectoryIterator($directory, FilesystemIterator::KEY_AS_FILENAME | FilesystemIterator::UNIX_PATHS);
$finder = new RecursiveIteratorIterator($finder);
$finder->setMaxDepth(3);

/** @var SplFileInfo $splFileInfo */
foreach ($finder as $file => $splFileInfo) {
    if ('composer.json' === $file) {
        $components[] = dirname($splFileInfo->getPathname());
    }
}

sort($components);

$githubActionsGroup = static function (string $component, string $message, int $exitCode) use ($runsOnGithubActions): string {
    if (! $runsOnGithubActions) {
        if ($exitCode > 0) {
            return sprintf("\n%1\$s\n+ %2\$s\n\033[41m FAIL \033[0m %1\$s\n\n", $component, $message);
        }

        if ($exitCode < 0) {
            return sprintf("\n%1\$s\n+ %2\$s\n\n\033[41m TIMEOUT \033[0m %1\$s\n\n", $component, $message);
        }

        return sprintf("\033[42m OK \033[0m %1\$s\n", $component);
    }

    if ($exitCode > 0) {
        return sprintf("%1\$s\n+ %2\$s\n\033[41m FAIL \033[0m %1\$s\n\n", $component, $message);
    }

    if ($exitCode < 0) {
        return sprintf("%1\$s\n+ %2\$s\n\033[41m TIMEOUT \033[0m %1\$s\n\n", $component, $message);
    }

    return sprintf(
        <<<EOF
            ::group::%1\$s
            + %2\$s

            \033[42m OK \033[0m %1\$s

            ::endgroup::

            EOF,
        $component,
        $message,
    );
};
$phpunitCommand = static function (string $component) use ($directory, $coverage): string {
    $coverageFile = sprintf('build/cov/%s-PHP_%s-%s.cov', basename($component), PHP_VERSION, PHP_OS_FAMILY);
    $cmd = implode(' ', [
        PHP_BINARY,
        'vendor/bin/phpunit',
        sprintf('--colors=%s', getenv('NO_COLOR') !== false ? 'never' : 'always'),
        '--group=unit-test',
        $coverage ? sprintf('--coverage-php %s', $coverageFile) : '--no-coverage',
        'tests/'.substr($component, strlen($directory) + 1),
    ]);
    $redirects = sprintf(' > %1$s/phpunit.stdout 2> %1$s/phpunit.stderr', $component);

    if (DIRECTORY_SEPARATOR === '\\') {
        $cmd = sprintf('cmd /v:on /d /c "(%s)%s"', $cmd, $redirects);
    } else {
        $cmd .= $redirects;
    }

    return $cmd;
};
$runningProcesses = [];

foreach ($components as $component) {
    $process = proc_open($phpunitCommand($component), [], $pipes);

    if (false !== $process) {
        $runningProcesses[$component] = $process;
    } else {
        $exit = 1;
        printf("\033[41m FAIL \033[0m %s\n", $component);
    }
}

$lastOutput = null;
$lastOutputTime = 0;

while ([] !== $runningProcesses) {
    usleep(300_000);
    $terminatedProcesses = [];

    foreach ($runningProcesses as $component => $process) {
        $processStatus = proc_get_status($process);

        if (! $processStatus['running']) {
            $terminatedProcesses[$component] = [$processStatus['command'], $processStatus['exitcode']];
            unset($runningProcesses[$component]);
            proc_close($process);
        }
    }

    if ([] === $terminatedProcesses && count($runningProcesses) === 1) {
        $component = key($runningProcesses);
        $process = $runningProcesses[$component];

        $output = file_get_contents(sprintf('%s/phpunit.stdout', $component));
        $output .= file_get_contents(sprintf('%s/phpunit.stderr', $component));

        if ($lastOutput !== $output) {
            $lastOutput = $output;
            $lastOutputTime = microtime(true);
        } elseif (microtime(true) - $lastOutputTime > 60) {
            if (DIRECTORY_SEPARATOR === '\\') {
                exec(sprintf('taskkill /F /T /PID %d 2>&1', proc_get_status($process)['pid']));
            } else {
                proc_terminate($process);
            }
        }
    }

    foreach ($terminatedProcesses as $component => [$command, $status]) {
        $output = $command."\n\n";

        foreach (['stdout', 'stderr'] as $file) {
            $file = sprintf('%s/phpunit.%s', $component, $file);
            $output .= file_get_contents($file);
            unlink($file);
        }

        if (0 !== $status) {
            $exit = $status;
        }

        echo $githubActionsGroup($component, $output, $status);
    }
}

exit($exit);
