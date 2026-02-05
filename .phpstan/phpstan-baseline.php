<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Method Jesperbeisner\\\\Fwstats\\\\Stdlib\\\\Database\\:\\:selectOne\\(\\) should return array\\{array\\<mixed\\>\\}\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../src/Stdlib/Database.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$serverParameters of class Jesperbeisner\\\\Fwstats\\\\Stdlib\\\\Request constructor expects array\\<string, string\\>, array\\<mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../src/Stdlib/Request.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$getParameters of class Jesperbeisner\\\\Fwstats\\\\Stdlib\\\\Request constructor expects array\\<string, string\\>, array\\<mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../src/Stdlib/Request.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$postParameters of class Jesperbeisner\\\\Fwstats\\\\Stdlib\\\\Request constructor expects array\\<string, string\\>, array\\<mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../src/Stdlib/Request.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$cookieParameters of class Jesperbeisner\\\\Fwstats\\\\Stdlib\\\\Request constructor expects array\\<string, string\\>, array\\<mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../src/Stdlib/Request.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#5 \\$headers of class Jesperbeisner\\\\Fwstats\\\\Stdlib\\\\Request constructor expects array\\<string, string\\>, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/../src/Stdlib/Request.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access an offset on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/../src/Stdlib/Session.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Jesperbeisner\\\\Fwstats\\\\Stdlib\\\\Session\\:\\:getFlash\\(\\) should return array\\<string\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/../src/Stdlib/Session.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
