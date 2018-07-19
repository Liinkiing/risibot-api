<?php


namespace App\Exporters;


use Goutte\Client;
use League\HTMLToMarkdown\HtmlConverter;
use Symfony\Component\DomCrawler\Crawler;

abstract class JvcTopicExporter
{
    protected const PAGES_LINK_SELECTOR = 'a.lien-jv';
    protected const ACTIVE_PAGE_SELECTOR = 'span.page-active';
    protected const REPLY_SELECTOR = '.bloc-message-forum ';

    protected $client;
    protected $baseUrl;
    protected $markdown = false;
    protected $converter;

    public function __construct()
    {
        $this->client = new Client();
        $this->converter = (new HtmlConverter())->setOptions(['strip_tags' => true]);
    }

    public function asMarkdown(): self
    {
        $this->markdown = true;

        return $this;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function setBaseUrl(string $baseUrl): self
    {
        $this->baseUrl = $baseUrl;

        return $this;
    }

    protected function getNextPageLink(Crawler $page): ?Crawler
    {
        $result = null;
        $page->filter(self::PAGES_LINK_SELECTOR)->each(function(Crawler $link) use ($page, &$result) {
            if ($this->getPageNumber($page) + 1 === (int) $link->text()) {
                $result = $link;
                return;
            }
        });

        return $result;
    }

    protected function getReplies(Crawler $page): Crawler
    {
        return $page->filter(self::REPLY_SELECTOR);
    }

    protected function getPageTitle(Crawler $page): string
    {
        return $page->filter('title')->text();
    }

    protected function hasNextPage(Crawler $page): bool
    {
        return $this->getNextPageLink($page) !== null;
    }

    protected function getPageNumber(Crawler $page): int
    {
        return (int) $page->filter(self::ACTIVE_PAGE_SELECTOR)->text();
    }

    abstract public function export(): array;

}