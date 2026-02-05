<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Image;

use GdImage;
use Jesperbeisner\Fwstats\Exception\ImageException;
use Jesperbeisner\Fwstats\Interface\ImageInterface;
use Jesperbeisner\Fwstats\Stdlib\Config;
use Webmozart\Assert\Assert;

abstract class AbstractImage implements ImageInterface
{
    protected ?GdImage $image = null;

    /** @var array<int> */
    protected array $colors = [];

    public function __construct(
        private readonly string $imageFolder,
        private readonly string $font,
    ) {
    }

    protected function createImage(int $width, int $height): void
    {
        Assert::positiveInteger($width);
        Assert::positiveInteger($height);

        if (false === $image = imagecreate($width, $height)) {
            throw new ImageException('Could not create an image.');
        }

        $this->image = $image;
    }

    protected function setBackgroundColor(int $color): void
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        imagefill($this->image, 0, 0, $color);
    }

    protected function write(string $text, int $x, int $y, ?int $size = null, ?int $color = null, int $angle = 0): void
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        $size = $size ?? 14;
        $color = $color ?? $this->colorBlack();

        if (false === imagettftext($this->image, $size, $angle, $x, $y, $color, $this->font, $text)) {
            throw new ImageException('Could not write to image.');
        }
    }

    protected function save(string $fileName): void
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        if (file_exists($fileName)) {
            if (false === unlink($fileName)) {
                throw new ImageException("Could not unlink '$fileName'.");
            }
        }

        if (false === imagepng($this->image, $fileName)) {
            throw new ImageException('Could not save the image.');
        }

        $this->colors = [];
        $this->image = null;
    }

    protected function colorBlack(): int
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        if (isset($this->colors['black'])) {
            return $this->colors['black'];
        }

        if (false === $color = imagecolorallocate($this->image, 0, 0, 0)) {
            throw new ImageException('Could not allocate image color.');
        }

        $this->colors['black'] = $color;

        return $color;
    }

    protected function colorWhite(): int
    {
        if ($this->image === null) {
            throw new ImageException("Did you forget to run 'imageCreate'?");
        }

        if (isset($this->colors['white'])) {
            return $this->colors['white'];
        }

        if (false === $color = imagecolorallocate($this->image, 255, 255, 255)) {
            throw new ImageException('Could not allocate image color.');
        }

        $this->colors['white'] = $color;

        return $color;
    }

    protected function getImageFolder(): string
    {
        return $this->imageFolder;
    }
}
