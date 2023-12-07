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

        // Check if we have new players and if they are relly new or just unbanned/undeleted
        $this->checkCreatedPlayers();

        // Check if we have removed players and if yes, check if they were deleted or banned
        $this->checkRemovedPlayers();

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

    private function checkCreatedPlayers(): void
    {
        DB::table('created_players_import')->truncate();

        // Add all players that are new in the players dump to a separate table
        DB::statement('INSERT INTO created_players_import (player_id, player_world) SELECT pi.id, pi.world FROM players p RIGHT JOIN players_import pi ON p.id = pi.id AND p.world = pi.world WHERE p.id IS NULL');

        /** @var array<int, object{player_id: int, player_world: string}> $createdPlayers */
        $createdPlayers = DB::select('SELECT player_id, player_world FROM created_players_import');

        // Check for all newly created players if they are really new or just unbanned/undeleted
        foreach ($createdPlayers as $createdPlayer) {
            /** @var null|object{id: int} $result */
            $result = DB::selectOne('SELECT id FROM removed_players WHERE player_id = :player_id AND player_world = :player_world AND updated_at IS NULL', ['player_id' => $createdPlayer->player_id, 'player_world' => $createdPlayer->player_world]);

            if ($result === null) {
                DB::insert('INSERT INTO created_players (player_id, player_world, created_at) VALUES (:player_id, :player_world, NOW())', ['player_id' => $createdPlayer->player_id, 'player_world' => $createdPlayer->player_world]);
            } else {
                DB::update('UPDATE removed_players SET updated_at = NOW() WHERE id = :id', ['id' => $result->id]);
            }
        }
    }

    private function checkRemovedPlayers(): void
    {
        // TODO: Implement me!!!
    }
}
