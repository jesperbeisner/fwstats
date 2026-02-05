<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Controller;

use Jesperbeisner\Fwstats\Attribute\LoginRequired;
use Jesperbeisner\Fwstats\Exception\RuntimeException;
use Jesperbeisner\Fwstats\Interface\ControllerInterface;
use Jesperbeisner\Fwstats\Interface\SessionInterface;
use Jesperbeisner\Fwstats\Repository\ConfigRepository;
use Jesperbeisner\Fwstats\Stdlib\Request;
use Jesperbeisner\Fwstats\Stdlib\Response;

/**
 * @see \Jesperbeisner\Fwstats\Tests\Application\AdminControllerTest
 */
#[LoginRequired]
final readonly class AdminController implements ControllerInterface
{
    public function __construct(
        private SessionInterface $session,
        private ConfigRepository $configRepository,
        private string $databaseFile,
    ) {
    }

    public function execute(Request $request): Response
    {
        if (null === $bearerToken = $this->session->getUser()?->token) {
            throw new RuntimeException('This should not be possible? Looks like you messed up once again! :^)');
        }

        return Response::html('admin/admin.phtml', [
            'bearerToken' => $bearerToken,
            'domainName' => $this->configRepository->findByKey('domain-name')->value ?? 'https://fwstats.de',
            'phpVersion' => phpversion(),
            'databaseSize' => $this->getDatabaseSize(),
        ]);
    }

    private function getDatabaseSize(): string
    {
        if (false === $databaseSize = filesize($this->databaseFile)) {
            throw new RuntimeException(sprintf('Could not get file size of database file "%s".', $this->databaseFile));
        }

        if ($databaseSize >= 1073741824) {
            return number_format($databaseSize / 1073741824, 2) . ' GB';
        }

        if ($databaseSize >= 1048576) {
            return number_format($databaseSize / 1048576, 2) . ' MB';
        }

        if ($databaseSize >= 1024) {
            return number_format($databaseSize / 1024, 2) . ' KB';
        }

        if ($databaseSize > 1) {
            return $databaseSize . ' bytes';
        }

        if ($databaseSize === 1) {
            return $databaseSize . ' byte';
        }

        return '0 bytes';
    }
}
