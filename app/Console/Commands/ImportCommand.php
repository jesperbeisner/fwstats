<?php

declare(strict_types=1);

namespace Fwstats\Console\Commands;

use Fwstats\Importers\ClanImporter;
use Fwstats\Importers\PlayerImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class ImportCommand extends Command
{
    protected $signature = 'fwstats:import';

    protected $description = 'Imports freewar stats';

    public function handle(ClanImporter $clanImporter, PlayerImporter $playerImporter): int
    {
        DB::table('clans_import')->truncate();
        DB::table('players_import')->truncate();

        $clans = $clanImporter->getClans();
        $players = $playerImporter->getPlayers();

        foreach ($clans as $world => $data) {
            foreach (array_chunk($data, 2500) as $values) {
                DB::table('clans_import')->insert($values);
            }
        }

        $this->info('Finished all clan imports');

        foreach ($players as $world => $data) {
            foreach (array_chunk($data, 2500) as $values) {
                DB::table('players_import')->insert($values);
            }
        }

        $this->info('Finished all player imports');

        DB::statement('INSERT INTO clans (id, world, name, shortcut, leader_id, co_leader_id, created_at, updated_at) SELECT id, world, name, shortcut, leader_id, co_leader_id, NOW(), NOW() FROM clans_import ON DUPLICATE KEY UPDATE clans.name = clans_import.name, clans.shortcut = clans_import.shortcut, clans.leader_id = clans_import.leader_id, clans.co_leader_id = clans_import.co_leader_id, clans.updated_at = NOW()');
        DB::statement('INSERT INTO players (id, world, name, xp, race, clan_id, soul_xp, profession, created_at, updated_at) SELECT id, world, name, xp, race, clan_id, soul_xp, profession, NOW(), NOW() FROM players_import ON DUPLICATE KEY UPDATE players.name = players_import.name, players.xp = players_import.xp, players.race = players_import.race, players.clan_id = players_import.clan_id, players.soul_xp = players_import.soul_xp, players.profession = players_import.profession, players.updated_at = NOW()');

        $this->info('Finished everything!');

        return Command::SUCCESS;

        // DB::statement('INSERT INTO new_players_import (player_id, player_world) SELECT pi.id, pi.world FROM players p RIGHT JOIN players_import pi ON p.id = pi.id AND p.world = pi.world WHERE p.id IS NULL');
        // DB::statement('INSERT INTO players (id, world, name, xp, race, created_at, updated_at) SELECT id, world, name, xp, race, NOW(), NOW() FROM players_import ON DUPLICATE KEY UPDATE players.name = players_import.name, players.xp = players_import.xp, players.race = players_import.race, players.updated_at = NOW()');
        // DB::statement('INSERT INTO new_players (player_id, player_world, created_at) SELECT new_players_import.player_id, new_players_import.player_world, NOW() FROM new_players_import');
    }
}
