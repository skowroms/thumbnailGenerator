<?php

namespace App\Tests\UnitTest;

use App;
use PHPUnit\Framework\TestCase;

class FileStorageTest extends TestCase
{

    private string $outputDirectoryPath = "tests/Resources/output/";

    protected function setUp(): void
    {
        parent::setUp();
        $this->clearOutputDirectory($this->outputDirectoryPath);
    }

    /**
     * @throws App\Exception\DirectoryReadError
     */
    public function testNotFoundHandling()
    {
        $storage = new App\GatewayImpl\FileStorageGateway();
        $this->expectException(App\Exception\OutputStorageIsNotWritable::class);
        $storage->getFiles('notFoundFolderPath');
    }

    /**
     * @throws App\Exception\OutputStorageIsNotWritable
     */
    public function testDirectoryReadError()
    {
        $storage = new App\GatewayImpl\FileStorageGateway();
        $this->expectException(App\Exception\DirectoryReadError::class);
        $storage->getFiles('tests/file');
    }

    /**
     * @throws App\Exception\DirectoryReadError
     * @throws App\Exception\OutputStorageIsNotWritable
     */
    public function testImageList()
    {
        $storage = new App\GatewayImpl\FileStorageGateway();
        $images = $storage->getFiles('tests/Resources/Images');

        $this->assertTrue(count($images) == 2);
        foreach ($images as $image) {
            $this->assertTrue(is_a($image, \SplFileInfo::class));
        }
    }

    public function testValidateDirectoryNotExist()
    {
        $storage = new App\GatewayImpl\FileStorageGateway();
        $this->assertFalse($storage->isOutputWritable('tests/Resources/notExistsFolder'));
        $this->assertFalse($storage->isOutputWritable('tests/Resources/file'));

        $this->assertTrue($storage->isOutputWritable('tests/Resources/output'));
    }

    public function testIsOutputWritable(){
        $storage = new App\GatewayImpl\FileStorageGateway();
        $this->assertFalse($storage->isOutputWritable('file'));
        $this->assertTrue($storage->isOutputWritable($this->outputDirectoryPath));
    }

    public function testSaveImage()
    {
        $storage = new App\GatewayImpl\FileStorageGateway();
        $storage->saveImage($this->outputDirectoryPath, 'old-red-door-1231703.jpg', file_get_contents('tests\Resources\Images\blob\testBlob'));
        $this->assertFileEquals('tests\Resources\images\resized\old-red-door-1231703.jpg', $this->outputDirectoryPath . 'old-red-door-1231703.jpg');
    }

    private function clearOutputDirectory(string $outputDirectoryPath)
    {
        $files = glob($outputDirectoryPath . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}