<?php

namespace App\Command;

use App\Entity\Risipic;
use App\Repository\RisipicRepository;
use App\Utils\Str;
use Doctrine\ORM\EntityManagerInterface;
use Goutte\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DomCrawler\Crawler;

class AppScrapRisibankCommand extends Command
{
    const BASE_URL = 'https://risibank.fr/';

    protected static $defaultName = 'app:scrap-risibank';

    protected $manager;
    protected $repository;
    protected $client;

    /**
     * @var SymfonyStyle
     */
    protected $io;

    public function __construct(EntityManagerInterface $manager, RisipicRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
        $this->client = new Client();


        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Scrap Risific website');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);

        $this->io->text('Scrapping of <info>' . self::BASE_URL . '</info> started...');

        $home = $this->client->request('GET', self::BASE_URL);
        $scripts = $home->filter('script');
        $scripts->each(function (Crawler $node) use (&$token) {
            if (Str::contains($node->text(), 'laravel_csrf')) {
                $token = Str::extractDoubleQuoted($node->text());
            }
        });

        $this->client->request('POST', self::BASE_URL . '/stickers/actualiser', [
            '_token' => $token ?? ''
        ]);

        $results = \GuzzleHttp\json_decode($this->client->getResponse()->getContent(), true)['data'];

        $this->io->section('LAST');
        $this->bulkInsert($this->getRisipics($results['last']));
        $this->io->section('RANDOM');
        $this->bulkInsert($this->getRisipics($results['rand']));
        $this->io->section('POPULAR');
        $this->bulkInsert($this->getRisipics($results['views']));

        $this->io->success('Successfully get stickers ! Now you can use some good risitas stickers made by kheys !');

    }

    /**
     * @param Risipic[] $risipics
     */
    private function bulkInsert(array $risipics): void
    {
        foreach ($risipics as $risipic) {
            if (!$this->repository->findOneBy(['url' => $risipic->getUrl()])) {
                $this->manager->persist($risipic);
                $this->io->text('Adding <info>' . $risipic->getUrl() . '</info> to database...');
            }
        }
        $this->manager->flush();
    }

    /**
     * @return Risipic[]
     */
    private function getRisipics(array $json): array
    {
        $risipics = [];
        foreach ($json as $item) {
            $url = $item['risibank_link'];
            $tags = $item['tags'];
            $views = $item['views'];
            $risipic = (new Risipic())
                ->setViews($views)
                ->setUrl($url)
                ->setTags(explode(' ', trim($tags)));
            $risipics[] = $risipic;
        }

        return $risipics;
    }
}
