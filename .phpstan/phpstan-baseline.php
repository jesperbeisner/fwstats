<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Call to function array_filter\\(\\) requires parameter \\#2 to be passed to avoid loose comparison semantics\\.$#',
	'identifier' => 'arrayFilter.strict',
	'count' => 1,
	'path' => __DIR__ . '/../config/app.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to function array_filter\\(\\) requires parameter \\#2 to be passed to avoid loose comparison semantics\\.$#',
	'identifier' => 'arrayFilter.strict',
	'count' => 2,
	'path' => __DIR__ . '/../config/database.php',
];
$ignoreErrors[] = [
	'message' => '#^Short ternary operator is not allowed\\. Use null coalesce operator if applicable or consider using long ternary\\.$#',
	'identifier' => 'ternary.shortNotAllowed',
	'count' => 1,
	'path' => __DIR__ . '/../config/debugbar.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$title of static method Illuminate\\\\Support\\\\Str\\:\\:slug\\(\\) expects string, bool\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../config/session.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
