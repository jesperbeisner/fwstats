<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Stdlib;

use Jesperbeisner\Fwstats\Exception\RuntimeException;
use Jesperbeisner\Fwstats\Service\RenderService;
use JsonException;

final class Response
{
    public const string CONTENT_TYPE_HTML = 'text/html; charset=UTF-8';
    public const string CONTENT_TYPE_TEXT = 'text/plain; charset=UTF-8';
    public const string CONTENT_TYPE_JSON = 'application/json; charset=UTF-8';
    public const string CONTENT_TYPE_IMAGE = 'image/png';

    private ?RenderService $renderService = null;

    /** @var array<string, string> */
    private array $headers = [];

    /** @var array<string, string> */
    private array $cookies = [];

    /**
     * @param array<string, mixed> $vars
     */
    private function __construct(
        public readonly int $statusCode,
        public readonly ?string $contentType = null,
        public readonly ?string $content = null,
        public readonly ?string $template = null,
        public readonly array $vars = [],
        public readonly ?string $location = null,
    ) {
    }

    /**
     * @param array<string, mixed> $vars
     */
    public static function html(string $template, array $vars = [], int $statusCode = 200): Response
    {
        return new Response(statusCode: $statusCode, contentType: Response::CONTENT_TYPE_HTML, template: $template, vars: $vars);
    }

    public static function text(string $content, int $statusCode = 200): Response
    {
        return new Response(statusCode: $statusCode, contentType: Response::CONTENT_TYPE_TEXT, content: $content);
    }

    /**
     * @param array<string, mixed> $vars
     */
    public static function json(array $vars = [], int $statusCode = 200): Response
    {
        try {
            $content = json_encode($vars, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException($e->getMessage());
        }

        return new Response(statusCode: $statusCode, contentType: Response::CONTENT_TYPE_JSON, content: $content);
    }

    public static function redirect(string $location, int $statusCode = 302): Response
    {
        return new Response(statusCode: $statusCode, location: $location);
    }

    public static function png(string $image, int $statusCode = 200, string $placeholderImage = __DIR__ . '/../../data/404-image.png'): Response
    {
        if (is_file($image)) {
            $content = file_get_contents($image);
            $contentLength = filesize($image);
        } else {
            $content = file_get_contents($placeholderImage);
            $contentLength = filesize($placeholderImage);
        }

        if ($content === false || $contentLength === false) {
            throw new RuntimeException(sprintf('"$content" or "$contentType" for image "%s" or placeholder image "%s" returned false.', $image, $placeholderImage));
        }

        $response = new Response(statusCode: $statusCode, contentType: Response::CONTENT_TYPE_IMAGE, content: $content);

        $response->setHeader('Cache-Control', 'no-cache');
        $response->setHeader('Content-Length', (string) $contentLength);

        return $response;
    }

    public function setRenderService(RenderService $renderService): void
    {
        $this->renderService = $renderService;
    }

    public function setHeader(string $key, string $value): void
    {
        $this->headers[$key] = $value;
    }

    public function setCookie(string $key, string $value): void
    {
        $this->cookies[$key] = $value;
    }

    /**
     * @return array<string, string>
     */
    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function send(): never
    {
        http_response_code($this->statusCode);

        foreach ($this->cookies as $key => $value) {
            setcookie($key, $value, ['expires' => time() + 60 * 60 * 24 * 365, 'path' => '/', 'secure' => true, 'httponly' => true]);
        }

        foreach ($this->headers as $key => $value) {
            header(sprintf('%s: %s', $key, $value));
        }

        if ($this->location !== null) {
            header(sprintf('Location: %s', $this->location));

            exit(0);
        }

        header(sprintf('Content-Type: %s', $this->contentType));

        $content = $this->content;

        if ($this->contentType === Response::CONTENT_TYPE_HTML) {
            if ($this->renderService === null) {
                throw new RuntimeException('Html response and RenderService was not set, how?');
            }

            if ($this->template === null) {
                throw new RuntimeException('Html response and we have no template set, how?');
            }

            $content = $this->renderService->render($this->template, $this->vars);
        }

        echo $content;

        exit(0);
    }
}
