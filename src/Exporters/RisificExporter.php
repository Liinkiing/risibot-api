<?php


namespace App\Exporters;


use App\Utils\Str;
use Symfony\Component\DomCrawler\Crawler;

class RisificExporter extends JvcTopicExporter
{

    public const RISIFIC_CHAPTER_CHARS_MIN = 500;
    public const RISIFIC_CHAPTER_STICKERS_MIN = 14;

    public function export(): array
    {
        $html = '';
        $page = $this->client->request('GET', $this->getBaseUrl());
        $title = $page->filter('title')->text();
        do {
            echo $this->getPageTitle($page) . "\n";

            foreach ($this->getChapters($page) as $chapter) {
                echo Str::truncate(trim($chapter->text()), 60) . "\n";
                $html .= $chapter->html() . '<br/><hr><br/>';
            }

            if ($nextPageLink = $this->getNextPageLink($page)) {
                $page = $this->client->click(
                    $nextPageLink->link()
                );
            }
        } while ($this->hasNextPage($page));

        return [
            'title' => $title,
            'content' => $this->markdown ? $this->converter->convert($html) : $html
        ];
    }

    public function getFilenameExtension (): string
    {
        return $this->markdown ? '.md' : '.html';
    }

    /**
     * @param Crawler $page
     * @return Crawler[]|array
     */
    private function getChapters(Crawler $page): array
    {
        $results = [];
        $this->getReplies($page)->each(function(Crawler $replyBloc) use (&$results) {
            $reply = $replyBloc->filter('.bloc-contenu .txt-msg');
            $wordsCount = str_word_count($reply->text());
            if ($wordsCount > self::RISIFIC_CHAPTER_CHARS_MIN && \count($this->getStickers($reply)) > self::RISIFIC_CHAPTER_STICKERS_MIN) {
                $results[] = $reply;
            }
        });

        return $results;
    }

    private function getStickers(Crawler $reply): array
    {
        $results = [];
        $reply->filter('.img-shack')->each(function (Crawler $sticker) use (&$results) {
            $results[] = $sticker->image()->getUri();
        });

        return $results;
    }

}