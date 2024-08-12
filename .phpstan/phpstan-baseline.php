<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	// identifier: identical.alwaysFalse
	'message' => '#^Strict comparison using \\=\\=\\= between false and string will always evaluate to false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../src/Service/RenderService.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
