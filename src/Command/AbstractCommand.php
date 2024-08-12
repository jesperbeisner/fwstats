<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Command;

use Jesperbeisner\Fwstats\Exception\RuntimeException;

abstract class AbstractCommand
{
    protected const int SUCCESS = 0;
    protected const int FAILURE = 1;

    public static string $name = '';
    public static string $description = '';

    /** @var string[] */
    protected array $arguments = [];
    protected ?float $time = null;

    abstract public function execute(): int;

    /**
     * @param string[] $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;
    }

    protected function writeLine(string $text = ''): void
    {
        if (false === file_put_contents('php://stderr', $text . PHP_EOL)) {
            throw new RuntimeException('Could not write to stderr');
        }
    }

    protected function startTime(): void
    {
        $this->time = microtime(true);
    }

    protected function getTime(): int
    {
        if ($this->time === null) {
            throw new RuntimeException("Did you forget to call 'startTime' before calling 'getTime'?");
        }

        return (int) round((microtime(true) - $this->time) * 1000);
    }
}
