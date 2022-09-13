<?php

namespace App\Command;

use App\Exception\IncorrectOutputStorage;
use App\Exception\OutputStorageIsNotWritable;
use App\Exception\DirectoryReadError;
use App\GatewayImpl\FileStorageGateway;
use App\GatewayImpl\s3BucketStorageGateway;
use App\Helper\ImageHelper;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateThumbnails extends Command
{
    private array $errors = [];
    private array $messages = [];

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $localDirectoryPath = $input->getArgument("localPath");
        $outputPath = $input->getArgument("outputPath");

        try {
            $outputStorage = $this->getOutputStorage($input->getArgument('storageType'));
            if (!$outputStorage->isOutputWritable($outputPath)) {
                throw new OutputStorageIsNotWritable();
            }

            $imageHelper = new ImageHelper();
            foreach ($this->getImageFiles($localDirectoryPath) as $file) {
                $blob = $imageHelper->getResizedBlob($file);
                if (!$outputStorage->saveImage($outputPath, $file->getFilename(), $blob)) {
                    $this->errors[] = $file->getFilename() . ' save error';
                } else {
                    $this->messages[] = 'File ' . $file->getFilename() . ' correctly resized and saved';
                }
            }

            if ($imageHelper->hasErrors()) {
                $this->errors = $imageHelper->getErrors();
            }

        } catch (IncorrectOutputStorage $e) {
            $this->errors[] = 'Incorrect Output Storage';
        } catch (OutputStorageIsNotWritable $e) {
            $this->errors[] = 'Output storage does not exist or is not writable';
        } catch (\Exception $e) {
            $this->errors[] = 'Internal server error';
        }

        if ($this->hasErrors()) {
            $this->writeMessages($this->errors, $output);
            return Command::FAILURE;
        }

        $this->writeMessages($this->messages, $output);
        return Command::SUCCESS;
    }

    private function getImageFiles($localDirectoryPath): array
    {
        try {
            $fileStorage = new FileStorageGateway();
            return $fileStorage->getImageFiles($localDirectoryPath);
        } catch (OutputStorageIsNotWritable $e) {
            $this->errors[] = 'Input directory not found';
        } catch (DirectoryReadError $e) {
            $this->errors[] = 'Can not read from directory';
        } catch (Exception $e) {
            $this->errors[] = 'Internal server error';
        }
        return array();
    }

    private function writeMessages(array $messages, $output)
    {
        foreach ($messages as $message) {
            $output->writeln($message);
        }
    }

    protected function configure(): void
    {
        $this->setHelp('This command allows you to generate thumbnails based on local directory and save it to somewhere');
        $this->addArgument('localPath', InputArgument::REQUIRED, 'The Local directory path with the image files you want to resize');
        $this->addArgument('storageType', InputArgument::REQUIRED, 'What type of storage do you want to use [\'local\', \'s3bucket\']');
        $this->addArgument('outputPath', InputArgument::REQUIRED, 'The output path to save images');
    }

    private function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * @throws \Exception
     */
    private function getOutputStorage($storageType)
    {
        switch ($storageType) {
            case 'local':
                return new FileStorageGateway();
            case 's3bucket':
                return new s3BucketStorageGateway();
            default:
                throw new IncorrectOutputStorage();
        }
    }
}