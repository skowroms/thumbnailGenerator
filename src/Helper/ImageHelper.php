<?php
namespace App\Helper;

use Exception;
use Imagick;
use SplFileInfo;

class ImageHelper
{
    private array $errors;
    private Imagick $thumb;

    public function __construct()
    {
        $this->errors = array();
        $this->thumb = new Imagick();
    }

    public function getResizedBlob(SplFileInfo $file): string
    {
        try {
            $this->thumb->clear();
            $this->thumb->readImage($file->getRealPath());
            $this->thumb->resizeImage(150, 150, Imagick::STYLE_NORMAL, 1, true);
            return $this->thumb->getImageBlob();
        } catch (Exception $e) {
            $this->errors[] = "Image '{$file->getFilename()}' not resized - error";
        }
        return '';
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }
}