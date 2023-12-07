<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$num of function round expects float\\|int, array\\|float given\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../app/Console/Commands/ImportCommand.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
