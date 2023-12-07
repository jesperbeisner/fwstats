<?php

declare(strict_types=1);

namespace Fwstats\Importers;

use Fwstats\Enums\WorldEnum;
use Illuminate\Support\Facades\DB;

final class ClanImporter extends AbstractImporter
{
    protected const string URL = 'https://{WORLD}.freewar.de/freewar/dump_clans.php';

    public function import(): void
    {
        DB::table('clans_import')->truncate();

        $clans = [];
        foreach (WorldEnum::cases() as $worldEnum) {
            $url = $this->getUrl($worldEnum);
            $dump = $this->getDump($url);
            $dump = trim($dump);

            $clans[$worldEnum->value] = $this->parseDump($worldEnum, $dump);
        }

        foreach ($clans as $world => $data) {
            foreach (array_chunk($data, 2500) as $values) {
                DB::table('clans_import')->insert($values);
            }
        }

        DB::statement('INSERT INTO clans (id, world, name, shortcut, leader_id, co_leader_id, created_at, updated_at) SELECT id, world, name, shortcut, leader_id, co_leader_id, NOW(), NOW() FROM clans_import ON DUPLICATE KEY UPDATE clans.name = clans_import.name, clans.shortcut = clans_import.shortcut, clans.leader_id = clans_import.leader_id, clans.co_leader_id = clans_import.co_leader_id, clans.updated_at = NOW()');
    }

    /**
     * @return array<int, array{id: int, world: string, name: string, shortcut: ?string, leader_id: ?int, co_leader_id: ?int}>
     */
    private function parseDump(WorldEnum $worldEnum, string $dump): array
    {
        $clans = [];

        foreach (explode("\n", $dump) as $line) {
            $data = explode("\t", $line);

            $clans[] = [
                'id' => (int) $data[0],
                'world' => $worldEnum->value,
                'name' => html_entity_decode($data[2]),
                'shortcut' => $data[1] !== '' ? html_entity_decode($data[1]) : null,
                'leader_id' => $data[3] !== '0' ? (int) $data[3] : null,
                'co_leader_id' => $data[4] !== '0' ? (int) $data[4] : null,
            ];
        }

        return $clans;
    }
}
