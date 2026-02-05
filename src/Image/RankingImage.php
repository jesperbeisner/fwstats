<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Image;

use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Exception\ImageException;
use Jesperbeisner\Fwstats\Model\Clan;
use Jesperbeisner\Fwstats\Model\Player;
use Jesperbeisner\Fwstats\Repository\ClanRepository;
use Jesperbeisner\Fwstats\Repository\PlayerRepository;

/**
 * @see \Jesperbeisner\Fwstats\Tests\Unit\Image\RankingImageTest
 */
final class RankingImage extends AbstractImage
{
    public function __construct(
        string $imageFolder,
        string $font,
        private readonly string $imageName,
        private readonly PlayerRepository $playerRepository,
        private readonly ClanRepository $clanRepository,
    ) {
        parent::__construct($imageFolder, $font);
    }

    public function create(WorldEnum $world): void
    {
        $players = $this->playerRepository->getTop50PlayersByWorld($world);
        $clans = $this->clanRepository->findAllByWorld($world);

        $this->createImage(750, (int) (105 + (count($players) * 19.1)));
        $this->setBackgroundColor($this->colorWhite());

        if ($world === WorldEnum::AFSRV) {
            $this->write('Top 50 ActionFreewar', 264, 25, 17);
        } else {
            $this->write('Top 50 ChaosFreewar', 262, 25, 17);
        }

        $this->createRankColumn($players);
        $this->createNameColumn($players);
        $this->createRaceColumn($players);
        $this->createClanColumn($players, $clans);
        $this->createProfessionColumn($players);
        $this->createXpColumn($players);
        $this->createSoulXpColumn($players);
        $this->createTotalXpColumn($players);
        $this->createSoulLevelColumn($players);

        $this->write('www.fwstats.de', 10, (int) (93 + (count($players) * 19.1)), 10);
        $this->write(date('Y-m-d - H:i:s'), 615, (int) (93 + (count($players) * 19.1)), 10);

        $this->save($this->getImageFolder() . sprintf('/%s-%s', $world->value, $this->imageName));
    }

    /**
     * @param Player[] $players
     */
    private function createRankColumn(array $players): void
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        $this->write('#', 15, 65, 12);

        for ($i = 1; $i <= count($players); $i++) {
            $this->write($i . '.', 15, 19 * $i + 70, 10);
        }
    }

    /**
     * @param Player[] $players
     */
    private function createNameColumn(array $players): void
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        $this->write('Name', 45, 65, 12);

        foreach ($players as $id => $player) {
            $name = strlen($player->name) > 20 ? substr($player->name, 0, 20) : $player->name;
            $this->write($name, 45, 19 * ($id + 1) + 70, 10);
        }
    }

    /**
     * @param Player[] $players
     */
    private function createRaceColumn(array $players): void
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        $this->write('Rasse', 170, 65, 12);

        foreach ($players as $id => $player) {
            $this->write($player->getRaceShortcut(), 170, 19 * ($id + 1) + 70, 10);
        }
    }

    /**
     * @param Player[] $players
     * @param Clan[] $clans
     */
    private function createClanColumn(array $players, array $clans): void
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        $this->write('Clan', 240, 65, 12);

        foreach ($players as $id => $player) {
            $clan = $player->clanId === null ? null : ($clans[$player->clanId] ?? null);
            $this->write($clan === null ? '-' : $clan->shortcut, 240, 19 * ($id + 1) + 70, 10);
        }
    }

    /**
     * @param Player[] $players
     */
    private function createProfessionColumn(array $players): void
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        $this->write('Beruf', 310, 65, 12);

        foreach ($players as $id => $player) {
            $this->write($player->profession ?? '-', 310, 19 * ($id + 1) + 70, 10);
        }
    }

    /**
     * @param Player[] $players
     */
    private function createXpColumn(array $players): void
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        $this->write('Xp', 440, 65, 12);

        foreach ($players as $id => $player) {
            $xp = number_format((float) $player->xp, 0, ',', '.');
            $this->write($xp, 440, 19 * ($id + 1) + 70, 10);
        }
    }

    /**
     * @param Player[] $players
     */
    private function createSoulXpColumn(array $players): void
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        $this->write('Soul-Xp', 530, 65, 12);

        foreach ($players as $id => $player) {
            $soulXp = number_format((float) $player->soulXp, 0, ',', '.');
            $this->write($soulXp, 530, 19 * ($id + 1) + 70, 10);
        }
    }

    /**
     * @param Player[] $players
     */
    private function createTotalXpColumn(array $players): void
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        $this->write('Total-Xp', 620, 65, 12);

        foreach ($players as $id => $player) {
            $totalXp = number_format((float) $player->totalXp, 0, ',', '.');
            $this->write($totalXp, 620, 19 * ($id + 1) + 70, 10);
        }
    }

    /**
     * @param Player[] $players
     */
    private function createSoulLevelColumn(array $players): void
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        $this->write('Stufe', 700, 65, 12);

        foreach ($players as $id => $player) {
            $this->write(Player::getSoulLevel($player->xp, $player->soulXp) === null ? '-' : (string) Player::getSoulLevel($player->xp, $player->soulXp), 718, 19 * ($id + 1) + 70, 10);
        }
    }
}
