<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Doubles;

use Jesperbeisner\Fwstats\Exception\DatabaseException;
use Jesperbeisner\Fwstats\Interface\DatabaseInterface;

final class DatabaseDummy implements DatabaseInterface
{
    /** @var array<mixed> */
    private array $selectReturn = [];

    /** @var null|array<mixed> */
    private null|array $selectOneReturn = null;

    /**
     * @param array<mixed> $selectReturn
     */
    public function setSelectReturn(array $selectReturn): void
    {
        $this->selectReturn = $selectReturn;
    }

    /**
     * @param array<mixed> $selectOneReturn
     */
    public function setSelectOneReturn(array $selectOneReturn): void
    {
        $this->selectOneReturn = $selectOneReturn;
    }

    public function execute(string $sql, array $params = []): void
    {
    }

    /**
     * @phpstan-pure
     */
    public function select(string $sql, array $params = []): array
    {
        return $this->selectReturn;
    }

    public function selectOne(string $sql, array $params = []): ?array
    {
        /** @phpstan-ignore-next-line */
        return $this->selectOneReturn;
    }

    public function insert(string $sql, array $params = []): int
    {
        return 1;
    }

    public function update(string $sql, array $params = []): void
    {
    }

    public function delete(string $sql, array $params = []): void
    {
    }

    public function beginTransaction(): void
    {
    }

    public function commitTransaction(): void
    {
    }

    public function rollbackTransaction(): void
    {
    }
}
