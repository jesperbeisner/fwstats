<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Service;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Exception\RuntimeException;
use Jesperbeisner\Fwstats\Interface\FreewarDumpServiceInterface;
use Jesperbeisner\Fwstats\Model\Clan;
use Jesperbeisner\Fwstats\Model\Player;

final readonly class FreewarDumpService implements FreewarDumpServiceInterface
{
    /** @var list<string> */
    private const array ADMINS = ['Sotrax', 'bwoebi', 'Nyrea', 'Andocai', 'alexa'];

    private const string PLAYERS_DUMP_URL = 'https://[WORLD].freewar.de/freewar/dump_players.php';
    private const string CLANS_DUMP_URL = 'https://[WORLD].freewar.de/freewar/dump_clans.php';
    private const string ACHIEVEMENTS_DUMP_URL = 'https://[WORLD].freewar.de/freewar/dump_achieves.php';

    public function getPlayersDump(WorldEnum $world): array
    {
        $players = [];
        foreach ($this->getDump($world, self::PLAYERS_DUMP_URL) as $playerDump) {
            $playerDump = trim($playerDump);
            $player = explode("\t", $playerDump);

            if (in_array($player[1], self::ADMINS, true)) {
                continue;
            }

            $players[(int) $player[0]] = new Player(
                id: null,
                world: $world,
                playerId: (int) $player[0],
                name: $player[1],
                race: $player[3],
                xp: (int) $player[2],
                soulXp: (int) $player[5],
                totalXp: (int) $player[2] + (int) $player[5],
                clanId: $player[4] === '0' ? null : (int) $player[4],
                profession: $player[6] ?? null,
                created: new DateTimeImmutable(),
            );
        }

        return $players;
    }

    public function getClansDump(WorldEnum $world): array
    {
        $clans = [];
        foreach ($this->getDump($world, self::CLANS_DUMP_URL) as $clanDump) {
            $clanDump = trim($clanDump);
            $clan = explode("\t", $clanDump);

            $clans[(int) $clan[0]] = new Clan(
                id: null,
                world: $world,
                clanId: (int) $clan[0],
                shortcut: $clan[1],
                name: $clan[2],
                leaderId: (int) $clan[3],
                coLeaderId: (int) $clan[4],
                diplomatId: (int) $clan[5],
                warPoints: (int) $clan[6],
                created: new DateTimeImmutable(),
            );
        }

        return $clans;
    }

    public function getAchievementsDump(WorldEnum $world): array
    {
        $achievements = [];
        foreach ($this->getDump($world, self::ACHIEVEMENTS_DUMP_URL) as $achievementDump) {
            $achievementDump = trim($achievementDump);
            $achievement = explode("\t", $achievementDump);

            $playerId = (int) $achievement[0];
            $achievementId = (int) $achievement[1];
            $achievementValue = (int) $achievement[2];

            $achievements[$playerId][$achievementId] = $achievementValue;
        }

        return $achievements;
    }

    /**
     * @return string[]
     */
    private function getDump(WorldEnum $world, string $url): array
    {
        $dumpUrl = str_replace('[WORLD]', $world->value, $url);

        // Retries for idiots :^)
        if (false === $dump = file_get_contents($dumpUrl)) {
            sleep(1);
            if (false === $dump = file_get_contents($dumpUrl)) {
                sleep(2);
                if (false === $dump = file_get_contents($dumpUrl)) {
                    throw new RuntimeException(sprintf('Could not get freewar dump url "%s".', $dumpUrl));
                }
            }
        }

        $dump = trim($dump);

        return explode("\n", $dump);
    }
}
