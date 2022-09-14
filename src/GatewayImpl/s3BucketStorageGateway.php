<?php

namespace App\GatewayImpl;

use App\Gateway\StorageGateway;
use Aws\Exception\MultipartUploadException;
use Aws\S3\S3Client;
use Symfony\Component\Filesystem\Path;

class s3BucketStorageGateway implements StorageGateway
{

    public function getFiles($directoryPath)
    {
        // TODO: Implement getImageFiles() method.
    }

    public function isOutputWritable($outputPath): bool
    {
        try {
            $this->getClient()->getBucketAcl(array("Bucket" => $_ENV['S3_BUCKET']));
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function saveImage(string $outputPath, string $fileName, string $blob): bool
    {
        try {
            $this->getClient()->putObject(array(
                'Bucket' => $_ENV['S3_BUCKET'],
                'Key' => Path::join($outputPath, $fileName),
                'Body' => $blob
            ));
            return true;
        } catch (MultipartUploadException $e) {
            return false;
        }
    }

    private function getClient(): S3Client
    {
        return new S3Client([
            'region' => $_ENV['S3_REGION'],
            'version' => $_ENV['S3_VERSION'],
            'credentials' => [
                'key' => $_ENV['S3_KEY'],
                'secret' => $_ENV['S3_SECRET']
            ],
        ]);
    }
}