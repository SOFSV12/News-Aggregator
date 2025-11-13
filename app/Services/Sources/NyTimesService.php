<?php 

namespace App\Services\Sources;

use Carbon\Carbon;
use App\Helpers\ArticleHelper;
use App\Interfaces\SourceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class NyTimesService implements SourceInterface 
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://api.nytimes.com/svc/news/v3';
        $this->apiKey = env('THE_NEW_YORK_TIMES_API_KEY');
    }

    public function fetchArticles(): array
    {
        $response = Http::withQueryParameters([
            'api-key' => $this->apiKey
        ])->get("{$this->baseUrl}/content/all/all.json");

        if ($response->failed()) {
            logger()->error('Source A API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [];
        }

        $data = $response->json();
        Log::info('NYTimesService fetched ' . count($data['results'] ?? []) . ' articles.');

        //return articles in standardized format
        return array_map(fn ($item) => [
        'source_name'       => 'New York Times',
        'source_identifier' => 'nytimes',
        'article_url'       => $item['url'] ?? '',
        'title'             => $item['title'] ?? '',
        'description'       => $item['abstract'] ?? null,
        'content'           => null,
        'author'            => ArticleHelper::extractAuthorNyTimes($item['byline'] ?? ''),
        'category'          => $item['section'] ?? null,
        'language'          => 'en',
        'image_url'         => ArticleHelper::extractImageUrlNyTimes($item['multimedia'] ?? []),
        'published_at'      => $item['published_date'] ? Carbon::parse($item['published_date']) : null,
        'fetched_at'        => now(),
        ], $data['results'] ?? []);
    }

}