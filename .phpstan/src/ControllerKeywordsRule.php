<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\PHPStan;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Class_>
 */
final class ControllerKeywordsRule implements Rule
{
    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $namespace = $scope->getNamespace();

        if ($namespace !== 'Jesperbeisner\Fwstats\Controller') {
            return [];
        }

        if ($node->isAbstract()) {
            return [
                RuleErrorBuilder::message('Abstract controllers are not allowed.')
                    ->identifier('fwstats')
                    ->build(),
            ];
        }

        if (!$node->isFinal()) {
            return [
                RuleErrorBuilder::message('Controller classes need to be final.')
                    ->identifier('fwstats')
                    ->build(),
            ];
        }

        if (!$node->isReadonly()) {
            return [
                RuleErrorBuilder::message('Controller classes need to be readonly.')
                    ->identifier('fwstats')
                    ->build(),
            ];
        }

        return [];
    }
}
