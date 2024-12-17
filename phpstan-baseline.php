<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Method Nexus\\\\Collection\\\\Collection\\:\\:all\\(\\) should return array\\<T\\> but returns array\\<TKey, T\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/src/Nexus/Collection/Collection.php',
];
$ignoreErrors[] = [
	'message' => '#^Function Nexus\\\\Option\\\\option\\(\\) never returns Nexus\\\\Option\\\\Some\\<T of mixed\\> so it can be removed from the return type\\.$#',
	'identifier' => 'return.unusedType',
	'count' => 1,
	'path' => __DIR__ . '/src/Nexus/Option/functions.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Nexus\\\\Tests\\\\AutoReview\\\\ComposerJsonTest\\:\\:getComposer\\(\\) should return array\\<string, mixed\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/tests/AutoReview/ComposerJsonTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function in_array\\(\\) with arguments string, array\\{\\} and true will always evaluate to false\\.$#',
	'identifier' => 'function.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/tests/AutoReview/TestCodeTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method Nexus\\\\Option\\\\Some\\<int\\>\\:\\:isNone\\(\\) will always evaluate to false\\.$#',
	'identifier' => 'method.impossibleType',
	'count' => 1,
	'path' => __DIR__ . '/tests/Option/OptionTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
