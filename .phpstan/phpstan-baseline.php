<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Call to function array_filter\\(\\) requires parameter \\#2 to be passed to avoid loose comparison semantics\\.$#',
	'identifier' => 'arrayFilter.strict',
	'count' => 1,
	'path' => __DIR__ . '/../config/app.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$string of function explode expects string, bool\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../config/app.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$title of static method Illuminate\\\\Support\\\\Str\\:\\:slug\\(\\) expects string, bool\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../config/cache.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function array_filter\\(\\) requires parameter \\#2 to be passed to avoid loose comparison semantics\\.$#',
	'identifier' => 'arrayFilter.strict',
	'count' => 2,
	'path' => __DIR__ . '/../config/database.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$title of static method Illuminate\\\\Support\\\\Str\\:\\:slug\\(\\) expects string, bool\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../config/database.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$string of function explode expects string, bool\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../config/logging.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$url of function parse_url expects string, bool\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../config/mail.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$title of static method Illuminate\\\\Support\\\\Str\\:\\:slug\\(\\) expects string, bool\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../config/session.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method comment\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/../routes/console.php',
];
$ignoreErrors[] = [
	'message' => '#^Undefined variable\\: \\$this$#',
	'identifier' => 'variable.undefined',
	'count' => 1,
	'path' => __DIR__ . '/../routes/console.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to method PHPUnit\\\\Framework\\\\Assert\\:\\:assertTrue\\(\\) with true will always evaluate to true\\.$#',
	'identifier' => 'method.alreadyNarrowedType',
	'count' => 1,
	'path' => __DIR__ . '/../tests/Unit/ExampleTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Dynamic call to static method PHPUnit\\\\Framework\\\\Assert\\:\\:assertTrue\\(\\)\\.$#',
	'identifier' => 'staticMethod.dynamicCall',
	'count' => 1,
	'path' => __DIR__ . '/../tests/Unit/ExampleTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
