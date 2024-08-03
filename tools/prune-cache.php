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

// ============================================================================
// This script flushes the GitHub Actions caches used by closed/merged PRs.
// It works by querying the REST API endpoints for GitHub Actions cache.
//
// @see https://docs.github.com/en/rest/actions/cache?apiVersion=2022-11-28#about-the-cache-in-github-actions
// ============================================================================

if ($argc < 2) {
    echo "\033[106;30m[USAGE]\033[0m \033[32mphp\033[0m \033[33m.github/prune-cache.php\033[0m <repo> [--pr-branch=BRANCH] [--schedule]\n";

    exit(1);
}

if ((bool) getenv('GITHUB_ACTIONS') && getenv('GH_TOKEN') === false) {
    echo "\033[97;41m[ERROR]\033[0m When running in GitHub Actions, please pass the \033[32mGH_TOKEN\033[0m environment variable.\n";

    exit(1);
}

$arguments = $argv;
array_shift($arguments);

$repository = '';
$branch = 0;
$onSchedule = false;
$parseOption = true;

foreach ($arguments as $index => $argument) {
    if ('--' === $argument) {
        $parseOption = false;

        continue;
    }

    if (str_starts_with($argument, '--') && $parseOption) {
        if (str_starts_with($argument, '--pr-branch=')) {
            $branch = (int) substr($argument, 13);

            continue;
        }

        if ('--pr-branch' === $argument) {
            $branch = (int) $arguments[$index + 1];

            continue;
        }

        if ('--schedule' === $argument) {
            $onSchedule = true;
        }

        continue;
    }

    if (0 === $index) {
        $repository = $argument;

        continue;
    }
}

$activeCacheUsageCommand = [
    'gh api',
    '-H "Accept: application/vnd.github+json"',
    '-H "X-GitHub-Api-Version: 2022-11-28"',
    sprintf('/repos/%s/actions/cache/usage', $repository),
    '2>/dev/null',
];
$cacheUsageOutput = (array) json_decode((string) shell_exec(implode(' ', $activeCacheUsageCommand)), true, flags: JSON_THROW_ON_ERROR);

if (
    isset($cacheUsageOutput['status'], $cacheUsageOutput['message'])
    && is_string($cacheUsageOutput['status'])
    && is_string($cacheUsageOutput['message'])
    && '200' !== $cacheUsageOutput['status']
) {
    echo sprintf(
        "\033[97;41m[ERROR]\033[0m %s (HTTP %d)\n",
        $cacheUsageOutput['message'],
        $cacheUsageOutput['status'],
    );

    exit(1);
}

assert(isset($cacheUsageOutput['active_caches_count'], $cacheUsageOutput['active_caches_size_in_bytes']));
$activeCachesCount = (int) $cacheUsageOutput['active_caches_count'];
$activeCachesSize = (float) $cacheUsageOutput['active_caches_size_in_bytes'];

echo sprintf(
    <<<EOF
        \033[32mRepository   :\033[0m %s
        \033[32mActive caches:\033[0m %d (%s MB)

        EOF,
    $repository,
    $activeCachesCount,
    number_format($activeCachesSize / 1_000_000, 2),
);

if ($branch < 1 && ! $onSchedule) {
    exit(0);
}

$cachesListCommand = static fn(int $page = 1): string => implode(' ', [
    'gh api',
    '-X GET',
    '-H "Accept: application/vnd.github+json"',
    '-H "X-GitHub-Api-Version: 2022-11-28"',
    '-F per_page=100',
    sprintf('-F page=%d', $page),
    $branch > 0 ? sprintf('-F ref=refs/pull/%d/merge', $branch) : '',
    sprintf('/repos/%s/actions/caches', $repository),
    '2>/dev/null',
]);
$cachesDeleteCommand = static fn(string $key, string $ref): string => implode(' ', [
    'gh api',
    '-X DELETE',
    '-H "Accept: application/vnd.github+json"',
    '-H "X-GitHub-Api-Version: 2022-11-28"',
    sprintf('-F ref=%s', $ref),
    sprintf('/repos/%s/actions/caches?key=%s', $repository, rawurlencode($key)),
    '2>/dev/null',
]);

/**
 * @var array{
 *   total_count: int,
 *   actions_caches: list<array{
 *     id: int,
 *     ref: string,
 *     key: string,
 *     version: string,
 *     last_accessed_at: string,
 *     created_at: string,
 *     size_in_bytes: int,
 *   }>
 * } $caches
 */
$caches = json_decode((string) shell_exec($cachesListCommand()), true, flags: JSON_THROW_ON_ERROR);
$counter = 0;
$roundTrips = $caches['total_count'] > 100 ? (int) ceil($caches['total_count'] / 100) : 1;

for ($page = 2; $page < $roundTrips; ++$page) {
    /**
     * @var array{
     *   total_count: int,
     *   actions_caches: list<array{
     *     id: int,
     *     ref: string,
     *     key: string,
     *     version: string,
     *     last_accessed_at: string,
     *     created_at: string,
     *     size_in_bytes: int,
     *   }>
     * } $output
     */
    $output = json_decode((string) shell_exec($cachesListCommand($page)), true, flags: JSON_THROW_ON_ERROR);
    $caches['actions_caches'] = array_merge($caches['actions_caches'], $output['actions_caches']);
}

foreach ($caches['actions_caches'] as $cache) {
    if (preg_match('#refs/pull/\d+/merge#', $cache['ref']) !== 1) {
        continue;
    }

    $exitCode = 0;
    $result = [];
    $message = sprintf(
        "Deleting cache \033[33m%s\033[0m (\033[31m%s MB\033[0m) on ref \033[32m%s\033[0m...\n",
        substr($cache['key'], 0, 50),
        number_format($cache['size_in_bytes'] / 1_000_000, 2),
        $cache['ref'],
    );

    echo $message;
    exec($cachesDeleteCommand($cache['key'], $cache['ref']), $result, $exitCode);

    if (0 === $exitCode) {
        echo "\033[1A";
        echo sprintf("\033[%dC", mb_strlen($message) - 27);
        echo "\033[32mDone\033[0m";
        echo "\033[1B";
        echo "\033[0G";
        ++$counter;
    }
}

echo sprintf(
    "\nDeleted \033[32m%d caches\033[0m from %s.\n",
    $counter,
    $branch > 0 ? sprintf('PR #%d branch', $branch) : ($onSchedule ? 'merged PR branches' : 'all branches'),
);
