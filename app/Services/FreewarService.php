<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\Player;
use App\Enums\ProfessionEnum;
use App\Enums\RaceEnum;
use App\Enums\WorldEnum;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

final readonly class FreewarService
{
    private const string PLAYERS_DUMP_URL = 'https://[WORLD].freewar.de/freewar/dump_players.php';

    /**
     * @return array<int, Player>
     */
    public function players(WorldEnum $world): array
    {
        $url = $this->getUrl(self::PLAYERS_DUMP_URL, $world);

        $dump = $this->getDump($url);

        $players = [];

        foreach ($dump as $line) {
            $playerParts = explode("\t", trim($line));

            $players[] = new Player(
                playerId: (int) $playerParts[0],
                name: $playerParts[1],
                race: RaceEnum::tryFrom($playerParts[3]),
                clanId: (int) $playerParts[4] === 0 ? null : (int) $playerParts[4],
                profession: ProfessionEnum::tryFrom((string) ($playerParts[6] ?? null)),
                xp: (int) $playerParts[2],
                soulXp: ($playerParts[5] ?? null) === null ? 0 : (int) $playerParts[5],
                totalXp: (int) $playerParts[2] + (($playerParts[5] ?? null) === null ? 0 : (int) $playerParts[5]),
            );
        }

        return $players;
    }

    private function getUrl(string $url, WorldEnum $world): string
    {
        return Str::replace('[WORLD]', $world->value, $url);
    }

    /**
     * @return array<int, string>
     */
    private function getDump(string $url): array
    {
        $response = Http::createPendingRequest()
            ->withUserAgent('fwstats')
            ->connectTimeout(3)
            ->timeout(5)
            ->retry(3, 3000, throw: false)
            ->get($url);

        if (!$response->ok()) {
            throw new RuntimeException('Could not get freewar dump');
        }

        $dump = trim($response->body());

        return explode("\n", $dump);
    }
}
