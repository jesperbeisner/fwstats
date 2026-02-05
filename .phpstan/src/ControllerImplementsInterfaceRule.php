<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\PHPStan;

use Jesperbeisner\Fwstats\Interface\ControllerInterface;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\InClassNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<InClassNode>
 */
final class ControllerImplementsInterfaceRule implements Rule
{
    public function getNodeType(): string
    {
        return InClassNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $classReflection = $scope->getClassReflection();

        if ($classReflection === null) {
            return [];
        }

        $className = $classReflection->getName();

        if (!str_starts_with($className, 'Jesperbeisner\Fwstats\Controller\\')) {
            return [];
        }

        if (str_starts_with($className, 'Jesperbeisner\Fwstats\Controller\Factory\\')) {
            return [];
        }

        if (!$classReflection->isSubclassOf(ControllerInterface::class)) {
            return [
                RuleErrorBuilder::message(sprintf('Controllers need to implement "%s".', ControllerInterface::class))
                    ->identifier('fwstats')
                    ->build(),
            ];
        }

        return [];
    }
}
