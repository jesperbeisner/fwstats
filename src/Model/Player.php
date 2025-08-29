<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Model;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use RuntimeException;

final readonly class Player
{
    public function __construct(
        public ?int $id,
        public WorldEnum $world,
        public int $playerId,
        public string $name,
        public string $race,
        public int $xp,
        public int $soulXp,
        public int $totalXp,
        public ?int $clanId,
        public ?string $profession,
        public DateTimeImmutable $created,
    ) {
    }

    public static function withId(int $id, Player $player): Player
    {
        return new Player(
            $id,
            $player->world,
            $player->playerId,
            $player->name,
            $player->race,
            $player->xp,
            $player->soulXp,
            $player->totalXp,
            $player->clanId,
            $player->profession,
            $player->created
        );
    }

    public function getRaceShortcut(): string
    {
        return match ($this->race) {
            'Onlo' => 'Onlo',
            'Natla - Händler' => 'Natla',
            'Mensch / Kämpfer' => 'M/K',
            'Mensch / Zauberer' => 'M/Z',
            'Mensch / Arbeiter' => 'M/A',
            'Keuroner' => 'Keuroner',
            'Taruner' => 'Taruner',
            'Serum-Geist' => 'Serum',
            'dunkler Magier' => 'D/M',
            default => throw new RuntimeException("New race in freewar? o.O")
        };
    }

    /*
     * You can certainly make it easier! But I do not know how... But it works :^)
     */
    public static function getSoulLevel(int $xp, int $soulXp): ?int
    {
        if ($xp !== 200_000) {
            return null;
        }

        if ($soulXp < 50_000) {
            return 0;
        }

        $level = 0;
        $sum = 0;
        $req = 50_000;

        while ($soulXp >= $sum + $req) {
            $sum += $req;
            $level++;
            $req = $level * 100_000;
        }

        return $level;
    }
}
