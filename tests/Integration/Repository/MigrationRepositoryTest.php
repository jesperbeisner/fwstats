<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Integration\Repository;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Model\Migration;
use Jesperbeisner\Fwstats\Repository\MigrationRepository;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MigrationRepository::class)]
final class MigrationRepositoryTest extends AbstractTestCase
{
    private MigrationRepository $migrationRepository;

    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();

        $this->migrationRepository = $this->getContainer()->get(MigrationRepository::class);
    }

    public function test_insert(): void
    {
        $migration = new Migration(null, 'test', new DateTimeImmutable());
        $newMigration = $this->migrationRepository->insert($migration);

        $this->assertNotNull($newMigration->id);
        $this->assertNotSame($migration, $newMigration);
    }

    public function test_findByFileName_returns_null_when_file_name_does_not_exist(): void
    {
        $migration = $this->migrationRepository->findByFileName('test');

        $this->assertNull($migration);
    }

    public function test_findByFileName_returns_the_migration_when_name_exists(): void
    {
        $this->migrationRepository->insert(new Migration(null, 'test', new DateTimeImmutable()));

        $migration = $this->migrationRepository->findByFileName('test');

        $this->assertNotNull($migration);
        $this->assertNotNull($migration->id);
    }
}
