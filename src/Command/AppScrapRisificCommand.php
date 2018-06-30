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

        $io->text('Scrapping of <info>' . self::BASE_URL . '</info> started...');

        $crawler = $this->client->request('GET', self::BASE_URL);
        $fics = $crawler->filter('#lcp_instance_0 li a');
        $io->progressStart($fics->count());

        $fics->each(function (Crawler $node) use ($io) {
            $url = $node->attr('href');
            $title = $node->text();
            $io->text("Getting <info>$title</info> (<comment>$url</comment>)...");
            if (!$this->repository->findOneBy(['url' => $url])) {
                $io->text("<info>$title</info> does not exist in database. Adding it...");
                $crawler = $this->client->request('GET', $url);
                $images = $crawler->filter('#content article img:first-of-type');
                $thumbnail = $images->count() > 0 ? $images->first()->attr('src') : 'https://i2.wp.com/image.noelshack.com/minis/2016/51/1482448857-celestinrisitas.png?resize=68%2C51&ssl=1';
                $fic = (new Risific())
                    ->setThumbnail($thumbnail)
                    ->setTitle($title)
                    ->setUrl($url);
                $this->manager->persist($fic);
                $this->manager->flush();
                $io->success("Successfully persisted $title !");
            } else {
                $io->text("<info>$title</info> already exist in database. Skipping it...");
            }
            $io->progressAdvance();
            $io->newLine();
        });
        $io->progressFinish();

        $io->success('Successfully get recent fictions ! Now you can read some good fictions made by kheys !');

    }
}
