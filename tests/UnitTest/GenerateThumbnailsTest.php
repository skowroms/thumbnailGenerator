<?php

namespace App\Tests\UnitTest;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateThumbnailsTest extends KernelTestCase
{
    private string $outputDirectoryPath = "tests/Resources/output/";

    protected function setUp(): void
    {
        parent::setUp();
        $this->clearOutputDirectory($this->outputDirectoryPath);
    }

    public function testEmptyInput()
    {
        $this->expectException('Symfony\Component\Console\Exception\RuntimeException');
        $this->expectErrorMessage('Not enough arguments (missing: "localPath, storageType, outputPath").');
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:generateThumbnails');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);
    }

    public function testToFewArguments()
    {
        $this->expectException('Symfony\Component\Console\Exception\RuntimeException');
        $this->expectErrorMessage('Not enough arguments (missing: "storageType, outputPath").');
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:generateThumbnails');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['localPath' => 'local',]);
    }

    public function testIncorrectArgument()
    {
        $this->expectException('Symfony\Component\Console\Exception\InvalidArgumentException');
        $this->expectErrorMessage('The "username" argument does not exist.');
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:generateThumbnails');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['username' => 'Wouter',]);
    }

    public function testNotFoundInputFolder()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:generateThumbnails');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['localPath' => 'local', 'storageType' => 'local', 'outputPath' => 'tests/Resources']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Input directory not found', $output);
    }

    public function testNotReadableInputFolder()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:generateThumbnails');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['localPath' => 'tests/Resources/file', 'storageType' => 'local', 'outputPath' => 'tests/Resources']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Can not read from directory', $output);
    }

    public function testIncorrectOutputStorage()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:generateThumbnails');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['localPath' => 'tests/Resources', 'storageType' => 'incorrect', 'outputPath' => '']);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Incorrect Output Storage', $output);
    }

    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:generateThumbnails');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['localPath' => 'tests/Resources/Images/', 'storageType' => 'local', 'outputPath' => 'tests/Resources/output']);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('File old-red-door-1231703.jpg correctly resized and saved File square-structure-1-1492321.jpg correctly resized and saved', trim(preg_replace('/\s\s+/', ' ', $output)));
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