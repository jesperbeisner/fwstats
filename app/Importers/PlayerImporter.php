<?php

declare(strict_types=1);

namespace Fwstats\Importers;

use Fwstats\Enums\RaceEnum;
use Fwstats\Enums\WorldEnum;

final class PlayerImporter extends AbstractImporter
{
    protected const string URL = 'https://{WORLD}.freewar.de/freewar/dump_players.php';

    /**
     * @return array<string, array<int, array{id: int, world: string, name: string, xp: int, race: string, clan_id: ?int, soul_xp: ?int, profession: ?string}>>
     */
    public function getPlayers(): array
    {
        $players = [];

        foreach (WorldEnum::cases() as $worldEnum) {
            $url = $this->getUrl($worldEnum);
            $dump = $this->getDump($url);
            $dump = trim($dump);

            $players[$worldEnum->value] = $this->parseDump($worldEnum, $dump);
        }

        return $players;
    }

    /**
     * @return array<int, array{id: int, world: string, name: string, xp: int, race: string, clan_id: ?int, soul_xp: ?int, profession: ?string}>
     */
    private function parseDump(WorldEnum $worldEnum, string $dump): array
    {
        $players = [];

        foreach (explode("\n", $dump) as $line) {
            $data = explode("\t", $line);

            // We only want real players/races and skip stuff like quest persons
            if (RaceEnum::tryFrom($data[3]) === null) {
                continue;
            }

            $players[] = [
                'id' => (int) $data[0],
                'world' => $worldEnum->value,
                'name' => $data[1],
                'xp' => (int) $data[2],
                'race' => $data[3],
                'clan_id' => array_key_exists(4, $data) && $data[4] !== '0' ? (int) $data[4] : null,
                'soul_xp' => array_key_exists(5, $data) && $data[5] !== '0' ? (int) $data[5] : null,
                'profession' => array_key_exists(6, $data) && $data[6] !== '' ? $data[6] : null,
            ];
        }

        return $players;
    }
}
