<?php

declare(strict_types=1);

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ]);

return (new PhpCsFixer\Config())->setRules([
    '@Symfony' => true,
    'strict_param' => true,
    'declare_strict_types' => true,
    'concat_space' => ['spacing' => 'one'],
    'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
    'phpdoc_align' => ['align' => 'left'],
    'global_namespace_import' => false,
    'nullable_type_declaration_for_default_null_value' => true,
    'is_null' => true,
    'new_with_parentheses' => [
        'named_class' => true,
        'anonymous_class' => true,
    ],
    'phpdoc_no_alias_tag' => ['replacements' => ['type' => 'var', 'link' => 'see']],
])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
    ->setParallelConfig(ParallelConfigFactory::detect())
;
