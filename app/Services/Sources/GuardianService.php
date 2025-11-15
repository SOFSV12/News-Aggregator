<?php 

namespace App\Services\Sources;

use Carbon\Carbon;
use App\Interfaces\SourceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Traits\ArticleFormatterTrait;

class GuardianService implements SourceInterface 
{
    use ArticleFormatterTrait;

    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = 'https://content.guardianapis.com';
        $this->apiKey = env('THE_GUARDIAN_API_KEY');
    }

    public function fetchArticles(): array
    {
        $response = Http::withQueryParameters([
            'api-key' => $this->apiKey
        ])->get("{$this->baseUrl}/search");

        if ($response->failed()) {
            logger()->error('Source A API request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [];
        }

        $data = $response->json();

        //return articles in standardized format
        return $this->formatArticlesArray($data['response']['results'] ?? [], 'The Guardian', 'guardian');
    }

}