<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	// identifier: return.unusedType
	'message' => '#^Method Nexus\\\\Option\\\\Choice\\:\\:from\\(\\) never returns Nexus\\\\Option\\\\Some\\<T of mixed\\> so it can be removed from the return type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Nexus/Option/Choice.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
