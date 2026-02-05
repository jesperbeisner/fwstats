<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Service;

use Jesperbeisner\Fwstats\DTO\Playtime;
use Jesperbeisner\Fwstats\Model\Player;
use Jesperbeisner\Fwstats\Repository\PlayerActiveSecondRepository;

final readonly class PlaytimeService
{
    public function __construct(
        private PlayerActiveSecondRepository $playerActiveSecondRepository,
    ) {
    }

    /**
     * @return array<int, Playtime|null>
     */
    public function getPlaytimesForPlayer(Player $player, int $days): array
    {
        $playtimes = $this->playerActiveSecondRepository->getPlaytimesForPlayer($player, $days);

        $result = [];

        for ($i = 1; $i < $days + 1; $i++) {
            if ($playtimes['day_' . $i] !== null && $playtimes['day_' . ($i + 1)] !== null) {
                $result[] = new Playtime($player->world, $player->name, $player->playerId, $playtimes['day_' . $i] - $playtimes['day_' . ($i + 1)]);
            } else {
                $result[] = null;
            }
        }

        return $result;
    }
}
