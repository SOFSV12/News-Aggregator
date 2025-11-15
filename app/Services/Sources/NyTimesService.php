<?php 

namespace App\Services\Sources;

use Carbon\Carbon;
use App\Helpers\ArticleHelper;
use App\Interfaces\SourceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Traits\ArticleFormatterTrait;

class NyTimesService implements SourceInterface 
{
    use ArticleFormatterTrait;
    
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

        //return articles in standardized format
        return $this->formatArticlesArray($data['results'] ?? [], 'New York Times', 'nytimes');
    }

}