<?php

declare(strict_types=1);

namespace Fwstats\Importers;

use Fwstats\Enums\WorldEnum;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractImporter
{
    protected const string URL = 'PLACEHOLDER';

    protected function getUrl(WorldEnum $worldEnum): string
    {
        if (static::URL === 'PLACEHOLDER') {
            throw new RuntimeException('You forget to overwrite the URL const! (╯°□°)╯︵ ┻━┻');
        }

        return str_replace('{WORLD}', $worldEnum->value, static::URL);
    }

    protected function getDump(string $url): string
    {
        // TODO: Timeout check einführen

        $response = Http::withUserAgent('fwstats.de')->get($url);

        if ($response->status() === Response::HTTP_OK) {
            return $response->body();
        }

        sleep(3);

        $response = Http::withUserAgent('fwstats.de')->get($url);

        if ($response->status() === Response::HTTP_OK) {
            return $response->body();
        }

        sleep(5);

        $response = Http::withUserAgent('fwstats.de')->get($url);

        if ($response->status() === Response::HTTP_OK) {
            return $response->body();
        }

        throw new RuntimeException(sprintf('Received status "%d" from freewar.de', $response->status()));
    }
}
