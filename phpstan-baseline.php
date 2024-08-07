<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	// identifier: return.unusedType
	'message' => '#^Method Nexus\\\\Option\\\\Choice\\:\\:from\\(\\) never returns Nexus\\\\Option\\\\Some\\<T of mixed\\> so it can be removed from the return type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/src/Nexus/Option/Choice.php',
];
$ignoreErrors[] = [
	// identifier: return.type
	'message' => '#^Method Nexus\\\\Tests\\\\AutoReview\\\\ComposerJsonTest\\:\\:getComposer\\(\\) should return array\\<string, mixed\\> but returns mixed\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/tests/AutoReview/ComposerJsonTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
