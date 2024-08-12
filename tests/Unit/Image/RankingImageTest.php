<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Image;

use Generator;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Image\RankingImage;
use Jesperbeisner\Fwstats\Repository\ClanRepository;
use Jesperbeisner\Fwstats\Repository\PlayerRepository;
use Jesperbeisner\Fwstats\Tests\Doubles\DatabaseDummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(RankingImage::class)]
final class RankingImageTest extends TestCase
{
    #[DataProvider('provideWorldEnumData')]
    public function test_it_creates_an_image_successfully(WorldEnum $worldEnum): void
    {
        $fileName = __DIR__ . sprintf('/../../../var/%s-ranking-test.png', $worldEnum->value);

        $database = new DatabaseDummy();
        $database->setSelectReturn([
            ['id' => 1, 'world' => $worldEnum->value, 'player_id' => 1, 'name' => 'Test-1', 'race' => 'Onlo', 'clan_id' => 1, 'profession' => 'Sammler', 'xp' => 100, 'soul_xp' => 100, 'total_xp' => 200, 'created' => date('Y-m-d H:i:s')],
            ['id' => 2, 'world' => $worldEnum->value, 'player_id' => 2, 'name' => 'Test-2', 'race' => 'Onlo', 'clan_id' => 2, 'profession' => null, 'xp' => 200, 'soul_xp' => 200, 'total_xp' => 400, 'created' => date('Y-m-d H:i:s')],
            ['id' => 3, 'world' => $worldEnum->value, 'player_id' => 3, 'name' => 'Test-3', 'race' => 'Onlo', 'clan_id' => null, 'profession' => 'Sammler', 'xp' => 200_000, 'soul_xp' => 300_000, 'total_xp' => 600_000, 'created' => date('Y-m-d H:i:s')],
        ]);

        $playerRepository = new PlayerRepository($database);

        $database = new DatabaseDummy();
        $database->setSelectReturn([
            ['id' => 1, 'world' => $worldEnum->value, 'clan_id' => 1, 'shortcut' => 'Test-1', 'name' => 'Test-1', 'leader_id' => 1, 'co_leader_id' => 1, 'diplomat_id' => 1, 'war_points' => 1, 'created' => date('Y-m-d H:i:s')],
            ['id' => 2, 'world' => $worldEnum->value, 'clan_id' => 2, 'shortcut' => 'Test-2', 'name' => 'Test-2', 'leader_id' => 2, 'co_leader_id' => 2, 'diplomat_id' => 2, 'war_points' => 2, 'created' => date('Y-m-d H:i:s')],
            ['id' => 3, 'world' => $worldEnum->value, 'clan_id' => 3, 'shortcut' => 'Test-3', 'name' => 'Test-3', 'leader_id' => 3, 'co_leader_id' => 3, 'diplomat_id' => 3, 'war_points' => 3, 'created' => date('Y-m-d H:i:s')],
        ]);

        $clanRepository = new ClanRepository($database);

        $rankingImage = new RankingImage(
            __DIR__ . '/../../../var',
            __DIR__ . '/../../../data/Roboto-Light.ttf',
            'ranking-test.png',
            $playerRepository,
            $clanRepository
        );

        $this->assertFileDoesNotExist($fileName);

        $rankingImage->create($worldEnum);

        $this->assertFileExists($fileName);
        $this->assertTrue(unlink($fileName));
    }

    /**
     * @return Generator<array<WorldEnum>>
     */
    public static function provideWorldEnumData(): Generator
    {
        yield 'ActionFreewar' => [WorldEnum::AFSRV];
        yield 'ChaosFreewar' => [WorldEnum::CHAOS];
    }
}
