<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\ImportPlayersAction;
use Illuminate\Console\Command;

final class ImportCommand extends Command
{
    protected $signature = 'app:import';

    public function handle(
        ImportPlayersAction $importPlayersAction,
    ): int {
        $importPlayersAction->handle();

        $this->output->success('Importiert!');

        return Command::SUCCESS;
    }
}
