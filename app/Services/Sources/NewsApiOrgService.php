<?php 

namespace App\Services\Sources;

use Carbon\Carbon;
use App\Interfaces\SourceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Traits\ArticleFormatterTrait;

class NewsApiOrgService implements SourceInterface 
{
    use ArticleFormatterTrait;
    
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
        return $this->formatArticlesArray($data['articles'] ?? [], 'NewsApiOrg', 'newsapi_org');
    }

}