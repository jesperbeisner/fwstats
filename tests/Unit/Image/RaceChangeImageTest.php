<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Image;

use Generator;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Image\RaceChangeImage;
use Jesperbeisner\Fwstats\Repository\PlayerRaceHistoryRepository;
use Jesperbeisner\Fwstats\Tests\Doubles\DatabaseDummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(RaceChangeImage::class)]
final class RaceChangeImageTest extends TestCase
{
    #[DataProvider('provideWorldEnumData')]
    public function test_it_creates_an_image_successfully(WorldEnum $worldEnum): void
    {
        $fileName = __DIR__ . sprintf('/../../../var/%s-race-changes-test.png', $worldEnum->value);

        $database = new DatabaseDummy();
        $database->setSelectReturn([
            ['world' => $worldEnum->value, 'player_id' => 1, 'name' => 'Test-1', 'old_race' => 'Onlo', 'new_race' => 'Mensch / Kämpfer', 'created' => date('Y-m-d H:i:s')],
            ['world' => $worldEnum->value, 'player_id' => 2, 'name' => 'Test-2', 'old_race' => 'Taruner', 'new_race' => 'Mensch / Zauberer', 'created' => date('Y-m-d H:i:s')],
            ['world' => $worldEnum->value, 'player_id' => 3, 'name' => 'Test-3', 'old_race' => 'Keuroner', 'new_race' => 'Mensch / Arbeiter', 'created' => date('Y-m-d H:i:s')],
        ]);

        $playerRaceHistoryRepository = new PlayerRaceHistoryRepository($database);

        $raceChangeImage = new RaceChangeImage(
            __DIR__ . '/../../../var',
            __DIR__ . '/../../../data/Roboto-Light.ttf',
            'race-changes-test.png',
            $playerRaceHistoryRepository
        );

        $this->assertFileDoesNotExist($fileName);

        $raceChangeImage->create($worldEnum);

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
