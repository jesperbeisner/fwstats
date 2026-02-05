<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Controller;

use Jesperbeisner\Fwstats\Interface\ControllerInterface;
use Jesperbeisner\Fwstats\Repository\ConfigRepository;
use Jesperbeisner\Fwstats\Stdlib\Request;
use Jesperbeisner\Fwstats\Stdlib\Response;

/**
 * @see \Jesperbeisner\Fwstats\Tests\Application\BanAndDeletionImageControllerTest
 */
final readonly class BanAndDeletionImageController implements ControllerInterface
{
    public function __construct(
        private ConfigRepository $configRepository,
    ) {
    }

    public function execute(Request $request): Response
    {
        return Response::html('image/bans-and-deletions.phtml', [
            'domainName' => $this->configRepository->findByKey('domain-name')->value ?? 'https://fwstats.de',
        ]);
    }
}
