<?php

namespace App\GatewayImpl;

use App\Exception\OutputStorageIsNotWritable;
use App\Exception\DirectoryReadError;
use App\Exception\SaveFileStorageError;
use App\Gateway\StorageGateway;
use FilesystemIterator;
use SplFileInfo;

class FileStorageGateway implements StorageGateway
{

    /**
     * @throws DirectoryReadError
     * @throws OutputStorageIsNotWritable
     */
    private function validateDirectory($directoryPath)
    {
        if (!file_exists($directoryPath)) {
            throw new OutputStorageIsNotWritable();
        }

        if (!is_dir($directoryPath) || !is_readable($directoryPath)) {
            throw new DirectoryReadError();
        }
    }

    /**
     * @return  SplFileInfo[]
     * @throws OutputStorageIsNotWritable
     * @throws DirectoryReadError
     */
    public function getImageFiles($directoryPath): array
    {
        $result = array();
        $this->validateDirectory($directoryPath);
        $iterator = new FilesystemIterator($directoryPath);

        foreach ($iterator as $entry) {
            if ($entry->isFile()) {
                $result[] = $entry;
            }
        }
        return $result;
    }

    public function isOutputWritable($outputPath): bool
    {
        return file_exists($outputPath) && is_dir($outputPath) && is_writable($outputPath);
    }

    public function saveImage(string $outputPath, string $fileName, string $blob): bool
    {
        try {
            file_put_contents($outputPath . '//' . $fileName, $blob);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

}

