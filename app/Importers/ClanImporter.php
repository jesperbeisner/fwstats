<?php

declare(strict_types=1);

namespace Fwstats\Importers;

use Fwstats\Enums\WorldEnum;

final class ClanImporter extends AbstractImporter
{
    protected const string URL = 'https://{WORLD}.freewar.de/freewar/dump_clans.php';

    /**
     * @return array<string, array<int, array{id: int, world: string, name: string, shortcut: ?string, leader_id: ?int, co_leader_id: ?int}>>
     */
    public function getClans(): array
    {
        $clans = [];

        foreach (WorldEnum::cases() as $worldEnum) {
            $url = $this->getUrl($worldEnum);
            $dump = $this->getDump($url);
            $dump = trim($dump);

            $clans[$worldEnum->value] = $this->parseDump($worldEnum, $dump);
        }

        return $clans;
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
