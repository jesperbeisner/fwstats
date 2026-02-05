<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Controller;

use Jesperbeisner\Fwstats\DTO\Playtime;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Interface\ControllerInterface;
use Jesperbeisner\Fwstats\Model\Player;
use Jesperbeisner\Fwstats\Repository\ClanRepository;
use Jesperbeisner\Fwstats\Repository\PlayerNameHistoryRepository;
use Jesperbeisner\Fwstats\Repository\PlayerProfessionHistoryRepository;
use Jesperbeisner\Fwstats\Repository\PlayerRaceHistoryRepository;
use Jesperbeisner\Fwstats\Repository\PlayerRepository;
use Jesperbeisner\Fwstats\Service\PlaytimeService;
use Jesperbeisner\Fwstats\Service\XpService;
use Jesperbeisner\Fwstats\Stdlib\Request;
use Jesperbeisner\Fwstats\Stdlib\Response;

/**
 * @see \Jesperbeisner\Fwstats\Tests\Application\ProfileControllerTest
 */
final readonly class ProfileController implements ControllerInterface
{
    public function __construct(
        private PlayerRepository $playerRepository,
        private ClanRepository $clanRepository,
        private XpService $xpService,
        private PlaytimeService $playtimeService,
        private PlayerNameHistoryRepository $playerNameHistoryRepository,
        private PlayerRaceHistoryRepository $playerRaceHistoryRepository,
        private PlayerProfessionHistoryRepository $playerProfessionHistoryRepository,
    ) {
    }

    public function execute(Request $request): Response
    {
        $world = $request->getRouteParameter('world');
        if (null === $world = WorldEnum::tryFrom($world)) {
            return Response::html('error/error.phtml', ['message' => 'text.404-page-not-found'], 404);
        }

        $playerId = $request->getRouteParameter('player-id');
        if (!is_numeric($playerId)) {
            return Response::html('error/error.phtml', ['message' => 'text.404-page-not-found'], 404);
        }

        if (null === $player = $this->playerRepository->find($world, (int) $playerId)) {
            return Response::html('error/error.phtml', ['message' => 'text.404-page-not-found'], 404);
        }

        $clan = $this->clanRepository->findByPlayer($player);

        $weeklyXpChanges = $this->xpService->getXpChangesForPlayer($player, 7);

        $weeklyPlaytimes = $this->playtimeService->getPlaytimesForPlayer($player, 7);
        [$totalPlaytime, $averagePlaytime] = $this->getTotalAndAveragePlaytime($player, $weeklyPlaytimes);

        $nameChanges = $this->playerNameHistoryRepository->getNameChangesForPlayer($player);
        $raceChanges = $this->playerRaceHistoryRepository->getRaceChangesForPlayer($player);
        $professionChanges = $this->playerProfessionHistoryRepository->getProfessionChangesForPlayer($player);

        return Response::html('profile/profile.phtml', [
            'player' => $player,
            'clan' => $clan,
            'weeklyXpChanges' => $weeklyXpChanges,
            'weeklyPlaytimes' => $weeklyPlaytimes,
            'totalPlaytime' => $totalPlaytime,
            'averagePlaytime' => $averagePlaytime,
            'nameChanges' => $nameChanges,
            'raceChanges' => $raceChanges,
            'professionChanges' => $professionChanges,
        ]);
    }

    /**
     * @param array<int, Playtime|null> $playtimes
     * @return array{Playtime, Playtime}
     */
    private function getTotalAndAveragePlaytime(Player $player, array $playtimes): array
    {
        $total = 0;
        $nullValues = 0;

        foreach ($playtimes as $playtime) {
            if ($playtime !== null) {
                $total += $playtime->getHours() * 60 * 60;
                $total += $playtime->getMinutes() * 60;
                $total += $playtime->getSeconds();
            } else {
                $nullValues++;
            }
        }

        $average = count($playtimes) - $nullValues === 0 ? 0 : (int) ($total / (count($playtimes) - $nullValues));

        $totalPlaytime = new Playtime($player->world, $player->name, $player->playerId, $total);
        $averagePlaytime = new Playtime($player->world, $player->name, $player->playerId, $average);

        return [$totalPlaytime, $averagePlaytime];
    }
}
