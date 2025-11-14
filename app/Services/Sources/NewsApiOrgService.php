<?php 

namespace App\Services\Sources;

use Carbon\Carbon;
use App\Interfaces\SourceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NewsApiOrgService implements SourceInterface 
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://newsapi.org';
        $this->apiKey = env('NEWSAPI_ORG_API_KEY');
    }

    public function fetchArticles(): array
    {

        $response = Http::withQueryParameters([
            'country' => 'us',
            'apiKey' => $this->apiKey,
        ])->get("{$this->baseUrl}/v2/top-headlines");

        if ($response->failed()) {
            logger()->error('Source A API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [];
        }

        $data = $response->json();
        

        //return articles in standardized format
        return array_map(fn ($item) => [
        'source_name'       => 'NewsApiOrg',
        'source_identifier' => 'newsapi_org',
        'article_url'       => $item['url'] ?? '',
        'title'             => $item['title'] ?? '',
        'description'       => $item['description'],
        'content'           => $item['content'],
        'author'            => $item['author'],
        'category'          => null,
        'language'          => null,
        'image_url'         => $item['urlToImage'],
        'published_at'      => $item['publishedAt'] ? Carbon::parse($item['publishedAt']) : null,
        'fetched_at'        => now(),
        ], $data['articles'] ?? []);
    }

}