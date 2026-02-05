<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Stdlib;

use Jesperbeisner\Fwstats\Enum\LocaleEnum;
use Jesperbeisner\Fwstats\Exception\RuntimeException;
use Jesperbeisner\Fwstats\Interface\TranslatorInterface;

final class Translator implements TranslatorInterface
{
    private LocaleEnum $defaultLocale = LocaleEnum::DE;
    private ?LocaleEnum $currentLocale = null;

    /** @var array<string, string> */
    private array $translations = [];

    public function __construct(
        private readonly string $translationsDirectory,
    ) {
    }

    public function translate(string $string, array $replacements = []): string
    {
        if ($this->translations === []) {
            $locale = $this->currentLocale ?? $this->defaultLocale;

            $translationsFileName = sprintf('%s/%s.php', $this->translationsDirectory, $locale->value);
            if (!is_file($translationsFileName)) {
                throw new RuntimeException(sprintf('Translation file "%s" does not exist.', $translationsFileName));
            }

            $translations = require $translationsFileName;
            if (!is_array($translations)) {
                throw new RuntimeException(sprintf('Translation file "%s" did not return an array.', $translationsFileName));
            }

            /** @var array<string, string> $translations */
            $this->translations = $translations;
        }

        if (!array_key_exists($string, $this->translations)) {
            return $string;
        }

        $translation = $this->translations[$string];

        foreach ($replacements as $key => $value) {
            $translation = str_replace($key, (string) $value, $translation);
        }

        return $translation;
    }

    public function setLocale(LocaleEnum $localeEnum): void
    {
        $this->currentLocale = $localeEnum;
    }

    public function getLocale(): LocaleEnum
    {
        return $this->currentLocale ?? $this->defaultLocale;
    }
}
