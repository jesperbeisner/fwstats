<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Integration\Repository;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Model\Cronjob;
use Jesperbeisner\Fwstats\Repository\CronjobRepository;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CronjobRepository::class)]
final class CronjobRepositoryTest extends AbstractTestCase
{
    private CronjobRepository $cronjobRepository;

    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();

        $this->cronjobRepository = $this->getContainer()->get(CronjobRepository::class);
    }

    public function test_insert(): void
    {
        $cronjob = new Cronjob(null, new DateTimeImmutable());
        $newCronjob = $this->cronjobRepository->insert($cronjob);

        $this->assertSame(1, $newCronjob->id);
        $this->assertNotSame($cronjob, $newCronjob);
    }

    public function test_findLastCronjob_returns_null_when_no_last_cronjob_exists(): void
    {
        $cronjob = $this->cronjobRepository->findLastCronjob();

        $this->assertNull($cronjob);
    }

    public function test_findLastCronjob_returns_the_last_cronjob(): void
    {
        $cronjobs = [
            new Cronjob(null, new DateTimeImmutable('2022-01-01 00:00:00')),
            new Cronjob(null, new DateTimeImmutable('2021-01-01 00:00:00')),
            new Cronjob(null, new DateTimeImmutable('2020-01-01 00:00:00')),
        ];

        foreach ($cronjobs as $cronjob) {
            $this->cronjobRepository->insert($cronjob);
        }

        $cronjob = $this->cronjobRepository->findLastCronjob();

        $this->assertNotNull($cronjob);
        $this->assertSame('2022-01-01 00:00:00', $cronjob->created->format('Y-m-d H:i:s'));
    }
}
