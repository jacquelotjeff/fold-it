<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class RenameByDateCommand extends Command
{
    protected static $defaultName = 'fold:rename-by-date';

    private $countProcessedFiles = 0;

    /** @var Filesystem */
    private $fileSystem;
    /** @var string */
    private $inputDir;
    /** @var string */
    private $outputDir;

    protected function configure(): void
    {
        $this
            ->setDescription('Rename your files from their date of creation in order to have your folders more organized.')
            ->addArgument('input_dir', InputArgument::REQUIRED, 'The folder containing the files.')
            ->addArgument('output_dir', InputArgument::REQUIRED, 'The dir to create new files.')
            ->addOption('timezone', 't', InputOption::VALUE_OPTIONAL, 'Timezone', 'Europe/Paris');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->inputDir = $input->getArgument('input_dir');
        $this->outputDir = $input->getArgument('output_dir');

        $this->fileSystem = new Filesystem();

        if (!$this->fileSystem->exists($this->inputDir)) {
            $output->writeln('The input dir doesn\'t exist.');
        }

        try {
            $this->fileSystem->mkdir($this->outputDir);
        } catch (IOException $exception) {
            $output->writeln(sprintf('The output dir cannot be created : %s', $exception->getMessage()));
            return 126;
        }

        if (!$this->fileSystem->exists($this->inputDir)) {
            $output->writeln('The input dir doesn\'t exist.');

            return 126;
        }

        if ($this->inputDir === $this->outputDir) {
            $output->writeln('Don\'t use input dir as outuput dir.');

            return 0;
        }

        // Set Timezone
        $timezone = $input->getOption('timezone');

        if (!date_default_timezone_set($timezone)) {
            $output->writeln(sprintf('The timezone %s doesn\'t seem to exist.', $timezone));

            return 126;
        }

        $finder = new Finder();
        $finder->in($this->inputDir);

        $this->process($finder);

        $output->writeln(sprintf('%d files were processed ;)', $this->countProcessedFiles));

        return 0;
    }

    private function process(Finder $finder): void
    {
        foreach ($finder as $file) {
            if ($file->isFile()) {
                $this->processFile($file);
            }
        }
    }

    private function processFile(\SplFileInfo $file): void
    {
        $this->countProcessedFiles++;
        $fileDate = filemtime($file->getRealPath());

        $date = date('Y-m-d H\hi', $fileDate);
        $year = date('Y', $fileDate);

        $extension = $file->getExtension();
        $commonFileName = $this->outputDir.'/'.$year.'/'.$date;

        $outputFile = $commonFileName.'.'.$extension;

        $salt = 1;
        while ($this->fileSystem->exists($outputFile)) {
            $salt++;
            $outputFile = $commonFileName.'-'.$salt.'.'.$extension;
        }

        $this->fileSystem->copy($file->getRealPath(), $outputFile);

        // Keep original creation date.
        touch($outputFile, $fileDate);
    }
}
