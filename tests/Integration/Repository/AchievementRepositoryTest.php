<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Integration\Repository;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Model\Achievement;
use Jesperbeisner\Fwstats\Model\Player;
use Jesperbeisner\Fwstats\Repository\AchievementRepository;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AchievementRepository::class)]
final class AchievementRepositoryTest extends AbstractTestCase
{
    private AchievementRepository $achievementRepository;

    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();

        $this->achievementRepository = $this->getContainer()->get(AchievementRepository::class);
    }

    public function test_insert(): void
    {
        $achievement = new Achievement(null, WorldEnum::AFSRV, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, new DateTimeImmutable());
        $newAchievement = $this->achievementRepository->insert($achievement);

        $this->assertSame(1, $newAchievement->id);
        $this->assertNotSame($achievement, $newAchievement);
    }

    public function test_findByPlayer_without_available_achievement(): void
    {
        $player = new Player(null, WorldEnum::AFSRV, 1, 'test', 'Onlo', 1, 1, 1, null, null, new DateTimeImmutable());

        $result = $this->achievementRepository->findByPlayer($player);

        $this->assertNull($result);
    }

    public function test_findByPlayer_with_available_achievement(): void
    {
        $achievement = new Achievement(null, WorldEnum::AFSRV, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, new DateTimeImmutable());
        $this->achievementRepository->insert($achievement);

        $player = new Player(null, WorldEnum::AFSRV, 1, 'test', 'Onlo', 1, 1, 1, null, null, new DateTimeImmutable());

        $result = $this->achievementRepository->findByPlayer($player);

        $this->assertInstanceOf(Achievement::class, $result);
        $this->assertSame(1, $result->playerId);
    }
}
