<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Service;

use Jesperbeisner\Fwstats\Service\RenderService;
use Jesperbeisner\Fwstats\Stdlib\Request;
use Jesperbeisner\Fwstats\Tests\Doubles\SessionFake;
use Jesperbeisner\Fwstats\Tests\Doubles\TranslatorDummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(RenderService::class)]
final class RenderServiceTest extends TestCase
{
    public function test_title_creation(): void
    {
        $viewRenderService = new RenderService('', '', new Request([], [], [], [], []), new SessionFake(), new TranslatorDummy());
        $this->assertSame('FWSTATS', $viewRenderService->getTitle());

        $viewRenderService->setTitle('Index');
        $this->assertSame('Index - FWSTATS', $viewRenderService->getTitle());
    }
}
