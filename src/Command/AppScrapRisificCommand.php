<?php

namespace App\Command;

use App\Entity\Risific;
use App\Repository\RisificRepository;
use Doctrine\ORM\EntityManagerInterface;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;

class AppScrapRisificCommand extends Command
{
    const BASE_URL = 'https://risific.fr/';

    protected static $defaultName = 'app:scrap-risifics';

    protected $manager;
    protected $repository;
    protected $client;

    public function __construct(EntityManagerInterface $manager, RisificRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->client = new Client();

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Scrap Risific website');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->text('Scrapping started...');

        $crawler = $this->client->request("GET", self::BASE_URL);
        $fics = $crawler->filter('#lcp_instance_0 li a');
        $progress = $io->createProgressBar($fics->count());
        $progress->display();

        $fics->each(function (Crawler $node) use ($io, $progress) {
            $url = $node->attr('href');
            $crawler = $this->client->request("GET", $url);
            $images = $crawler->filter('#content article img:first-of-type');
            $thumbnail = $images->count() > 0 ? $images->first()->attr('src') : 'https://i2.wp.com/image.noelshack.com/minis/2016/51/1482448857-celestinrisitas.png?resize=68%2C51&ssl=1';
            $title = $node->text();
            $io->text("Getting $title ($url)...");
            if (!$this->repository->findOneBy(['url' => $url])) {
                $io->text("$title does not exist in database. Adding it...");
                $fic = (new Risific())
                    ->setThumbnail($thumbnail)
                    ->setTitle($title)
                    ->setUrl($url);
                $this->manager->persist($fic);
                $io->success("Successfully persisted $title !");
            } else {
                $io->text("$title already exist in database. Skipping it...");
            }
            $progress->advance();
        });
        $this->manager->flush();
        $progress->finish();

        $io->success("Successfully get recent fictions ! Now you can read some good fictions made by kheys !");

    }
}
