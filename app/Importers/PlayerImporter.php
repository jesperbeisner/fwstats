<?php

declare(strict_types=1);

namespace Fwstats\Importers;

use Fwstats\Enums\RaceEnum;
use Fwstats\Enums\WorldEnum;
use Illuminate\Support\Facades\DB;

final class PlayerImporter extends AbstractImporter
{
    protected const string URL = 'https://{WORLD}.freewar.de/freewar/dump_players.php';

    public function import(): void
    {
        DB::table('players_import')->truncate();

        $players = [];
        foreach (WorldEnum::cases() as $worldEnum) {
            $url = $this->getUrl($worldEnum);
            $dump = $this->getDump($url);
            $dump = trim($dump);

            $players[$worldEnum->value] = $this->parseDump($worldEnum, $dump);
        }

        foreach ($players as $world => $data) {
            foreach (array_chunk($data, 2500) as $values) {
                DB::table('players_import')->insert($values);
            }
        }

        DB::statement('INSERT INTO players (id, world, name, xp, race, clan_id, soul_xp, profession, created_at, updated_at) SELECT id, world, name, xp, race, clan_id, soul_xp, profession, NOW(), NOW() FROM players_import ON DUPLICATE KEY UPDATE players.name = players_import.name, players.xp = players_import.xp, players.race = players_import.race, players.clan_id = players_import.clan_id, players.soul_xp = players_import.soul_xp, players.profession = players_import.profession, players.updated_at = NOW()');

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
