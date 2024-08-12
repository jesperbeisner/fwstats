<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Service;

use Jesperbeisner\Fwstats\Enum\PlayerStatusEnum;
use Jesperbeisner\Fwstats\Exception\RuntimeException;
use Jesperbeisner\Fwstats\Interface\PlayerStatusServiceInterface;
use Jesperbeisner\Fwstats\Model\Player;

final readonly class PlayerStatusService implements PlayerStatusServiceInterface
{
    private const string PROFILE_URL = 'https://%WORLD%.freewar.de/freewar/internal/fight.php?action=watchuser&act_user_id=%PLAYER_ID%';

    public function getStatus(Player $player): PlayerStatusEnum
    {
        $profileUrl = str_replace('%WORLD%', $player->world->value, PlayerStatusService::PROFILE_URL);
        $profileUrl = str_replace('%PLAYER_ID%', (string) $player->playerId, $profileUrl);

        if (false === $profile = file_get_contents($profileUrl)) {
            throw new RuntimeException(sprintf('Could not get profile from player with id "%d" and world "%s".', $player->playerId, $player->world->value));
        }

        if (str_contains($profile, sprintf('<p class="maindesc1">%s</p>', $player->name))) {
            return PlayerStatusEnum::BANNED;
        }

        if (str_contains($profile, sprintf('<p class="maindesc1">%s ba%d</p>', $player->name, $player->playerId))) {
            return PlayerStatusEnum::DELETED;
        }

        return PlayerStatusEnum::UNKNOWN;
    }
}
