<?php

namespace App\Gateway;

interface StorageGateway
{
    public function getImageFiles($directoryPath);

    public function isOutputWritable($outputPath): bool;

    public function saveImage(string $outputPath, string $fileName, string $blob): bool;

}