<?php 

namespace App\Services\Sources;

use Carbon\Carbon;
use App\Interfaces\SourceInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class GuardianService implements SourceInterface 
{
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
        return array_map(fn ($item) => [
        'source_name'       => 'The Guardian',
        'source_identifier' => 'guardian',
        'article_url'       => $item['webUrl'] ?? '',
        'title'             => $item['webTitle'] ?? '',
        'description'       => $item['abstract'] ?? null,
        'content'           => null,
        'author'            => '',
        'category'          => $item['sectionName'] ?? null,
        'language'          => null,
        'image_url'         => null,
        'published_at'      => $item['webPublicationDate'] ? Carbon::parse($item['webPublicationDate']) : null,
        'fetched_at'        => now(),
        ],$data['response']['results'] ?? []);
    }

}