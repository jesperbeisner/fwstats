<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\WorldEnum;
use App\Importers\PlayerImporter;
use Illuminate\Console\Command;

final class ImportCommand extends Command
{
    protected $signature = 'app:import';

    protected $description = 'Import Command';

    public function handle(PlayerImporter $playerImporter): int
    {
        foreach (WorldEnum::cases() as $world) {
            $playerImporter->import($world);

            $this->info('Finished world: ' . $world->value);
        }

        $this->info('Finished import');

        return Command::SUCCESS;
    }
}
