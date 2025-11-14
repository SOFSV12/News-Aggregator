<?php 

namespace App\Services\Sources;

use Carbon\Carbon;
use App\Helpers\ArticleHelper;
use App\Interfaces\SourceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NewsApiAiService implements SourceInterface 
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://eventregistry.org';
        $this->apiKey = env('NEWSAPI_AI_API_KEY');
    }

    public function fetchArticles(): array
    {
        $query = [
            '$query' => [
                'conceptUri' => 'http://en.wikipedia.org/wiki/Politics',
            ],
            '$filter' => [
                'forceMaxDataTimeWindow' => '31',
            ],
        ];

        $response = Http::withQueryParameters([
            'query' => json_encode($query),
            'resultType' => 'articles',
            'articlesSortBy' => 'date',
            'apiKey' => $this->apiKey,
        ])->get("{$this->baseUrl}/api/v1/article/getArticles");

        if ($response->failed()) {
            logger()->error('Source A API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [];
        }

        $data = $response->json();
        Log::info('NewsAPIAIService fetched ' . count($data['results'] ?? []) . ' articles.');

        //return articles in standardized format
        return array_map(fn ($item) => [
        'source_name'       => 'NewsApiAi',
        'source_identifier' => 'newsapi_ai',
        'article_url'       => $item['url'] ?? null,
        'title'             => $item['title'] ?? null,
        'description'       => $item['description'] ?? null,
        'content'           => $item['body'] ?? null,
        'author'            => ArticleHelper::extractAuthorsNeswApiAiService($item['authors'] ?? []),
        'category'          => $item['category'] ?? null,
        'language'          => $item['lang'] ?? null,
        'image_url'         => $item['image'] ?? null,
        'published_at'      => isset($item['dateTimePub']) ? Carbon::parse($item['dateTimePub']) : null,
        'fetched_at'        => now(),
        ], $data['articles']['results'] ?? []);
    }

}