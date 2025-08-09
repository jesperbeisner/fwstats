<?php

declare(strict_types=1);

namespace App\Importers;

use App\Enums\ProfessionEnum;
use App\Enums\RaceEnum;
use App\Enums\WorldEnum;
use App\Interfaces\ImporterInterface;
use App\Models\Player;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

final readonly class PlayerImporter implements ImporterInterface
{
    private const string DUMP_URL = 'https://[WORLD].freewar.de/freewar/dump_players.php';

    public function import(WorldEnum $world): void
    {
        ini_set('memory_limit', '256M');

        $playersDump = $this->getPlayersDump($world);

        DB::transaction(function () use ($world, $playersDump) {
            Player::query()
                ->where('world', '=', $world)
                ->delete();

            foreach (array_chunk($playersDump, 100) as $playersDumpChunked) {
                $players = [];

                foreach ($playersDumpChunked as $playerDump) {
                    $players[] = [
                        'world' => $playerDump->world,
                        'player_id' => $playerDump->player_id,
                        'name' => $playerDump->name,
                        'race' => $playerDump->race,
                        'clan_id' => $playerDump->clan_id,
                        'profession' => $playerDump->profession,
                        'xp' => $playerDump->xp,
                        'soul_xp' => $playerDump->soul_xp,
                        'total_xp' => $playerDump->total_xp,
                        'created_at' => $playerDump->created_at,
                        'updated_at' => $playerDump->updated_at,
                    ];
                }

                Player::insert($players);
            }
        });
    }

    /**
     * @return array<int, Player>
     */
    private function getPlayersDump(WorldEnum $world): array
    {
        $url = Str::replace('[WORLD]', $world->url(), self::DUMP_URL);

        $response = Http::createPendingRequest()
            ->withUserAgent('fwstats')
            ->get($url);

        $playersDump = $response->body();
        $playersDump = explode("\n", trim($playersDump));
        $playersDump = array_map(fn ($playerDump) => explode("\t", trim($playerDump)), $playersDump);

        $players = [];

        $date = new CarbonImmutable();

        foreach ($playersDump as $playerDump) {
            if (null === $race = RaceEnum::fromDump($playerDump[3])) {
                continue;
            }

            $xp = (int) $playerDump[2];
            $soulXp = ($playerDump[5] ?? null) === null ? 0 : (int) $playerDump[5];

            $player = new Player();

            $player->world = $world;
            $player->player_id = (int) $playerDump[0];
            $player->name = $playerDump[1];
            $player->race = $race;
            $player->clan_id = $playerDump[4] === '0' ? null : (int) $playerDump[4];
            $player->profession = ($playerDump[6] ?? null) === null ? null : ProfessionEnum::from($playerDump[6]);
            $player->xp = $xp;
            $player->soul_xp = $soulXp;
            $player->total_xp = $xp + $soulXp;
            $player->created_at = $date;
            $player->updated_at = $date;

            $players[$player->player_id] = $player;
        }

        ksort($players);

        return $players;
    }
}
