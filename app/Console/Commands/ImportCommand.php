<?php

declare(strict_types=1);

namespace Fwstats\Console\Commands;

use Fwstats\Importers\ClanImporter;
use Fwstats\Importers\PlayerImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Benchmark;

final class ImportCommand extends Command
{
    protected $signature = 'fwstats:import';

    protected $description = 'Imports freewar stats';

    public function handle(ClanImporter $clanImporter, PlayerImporter $playerImporter): int
    {
        $clanImportResult = Benchmark::measure(static fn () => $clanImporter->import());
        $this->info(sprintf('Finished all clan imports in %sms', round($clanImportResult)));

        $playerImportResult = Benchmark::measure(static fn () => $playerImporter->import());
        $this->info(sprintf('Finished all player imports in %sms', round($playerImportResult)));

        $this->info(sprintf('Finished all imports in %sms', round($clanImportResult + $playerImportResult)));

        return Command::SUCCESS;

        // DB::statement('INSERT INTO new_players_import (player_id, player_world) SELECT pi.id, pi.world FROM players p RIGHT JOIN players_import pi ON p.id = pi.id AND p.world = pi.world WHERE p.id IS NULL');
        // DB::statement('INSERT INTO players (id, world, name, xp, race, created_at, updated_at) SELECT id, world, name, xp, race, NOW(), NOW() FROM players_import ON DUPLICATE KEY UPDATE players.name = players_import.name, players.xp = players_import.xp, players.race = players_import.race, players.updated_at = NOW()');
        // DB::statement('INSERT INTO new_players (player_id, player_world, created_at) SELECT new_players_import.player_id, new_players_import.player_world, NOW() FROM new_players_import');
    }
}
