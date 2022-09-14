<?php

namespace App\Tests\UnitTest;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

class PathTest extends TestCase
{

    public function testNotFoundHandling()
    {
        $this->assertEquals('C:/FIRSTFolder/second/sub-folder/folder/file.txt', Path::join("C:\FIRSTFolder", "/second", "sub-folder/folder/", "file.txt"));
        $this->assertEquals('/var/wwww/sub-folder/folder/file.txt', Path::join("/var", "wwww", "sub-folder/folder/", "file.txt"));
    }
}