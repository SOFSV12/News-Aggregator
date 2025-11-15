<?php 

namespace App\Services\Sources;

use Carbon\Carbon;
use App\Helpers\ArticleHelper;
use App\Interfaces\SourceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Traits\ArticleFormatterTrait;

class NewsApiAiService implements SourceInterface 
{
    use ArticleFormatterTrait;
    
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

        //return articles in standardized format
        return $this->formatArticlesArray($data['articles']['results'] ?? [], 'NewsApiAi', 'newsapi_ai');
    }

}