<?php

namespace App\Command;

use App\Exporters\RisificExporter;
use App\Utils\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class AppExportRisificCommand extends Command
{
    protected static $defaultName = 'app:export-risific';
    protected $exporter;
    protected $projectDir;
    protected $filesystem;

    public function __construct(RisificExporter $exporter, Filesystem $filesystem, string $projectDir)
    {
        $this->exporter = $exporter;
        $this->projectDir = $projectDir;
        $this->filesystem = $filesystem;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Allows to export to markdown a complete risific based on a topic')
            ->addArgument('topic_url', InputArgument::REQUIRED, 'Put here the topic where the fic is')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $url = $input->getArgument('topic_url');
        $io->text("Extracting fic from <info>$url</info>... ");

        $fic = $this->exporter
            ->setBaseUrl($url)
            ->asMarkdown()
            ->export();

        $filename = $this->getFilepath() . '/' . Str::slugify($fic['title']) . $this->exporter->getFilenameExtension();
        $this->filesystem->dumpFile($filename, $fic['content']);

        $io->success('Successfully extracted ' . $fic['title'] . ' into html file. ' . $filename);
    }

    protected function getFilepath(): string
    {
        return $this->projectDir . '/public/risifics';
    }

}
