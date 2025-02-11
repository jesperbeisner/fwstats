<?php

declare(strict_types=1);

namespace App\Actions;

use App\Data\Player;
use App\Enums\WorldEnum;
use App\Models\PlayerImport;
use App\Services\FreewarService;

final readonly class ImportPlayersAction
{
    public function __construct(
        private FreewarService $freewarService,
    ) {
    }

    public function handle(): void
    {
        PlayerImport::truncate();

        foreach (WorldEnum::cases() as $world) {
            $players = $this->freewarService->players($world);

            PlayerImport::insert(array_map(function (Player $player) use ($world) {
                return [
                    'world' => $world->value,
                    'player_id' => $player->playerId,
                    'name' => $player->name,
                    'race' => $player->race?->value,
                    'clan_id' => $player->clanId,
                    'profession' => $player->profession?->value,
                    'xp' => $player->xp,
                    'soul_xp' => $player->soulXp,
                    'total_xp' => $player->totalXp,
                ];
            }, $players));
        }
    }
}
