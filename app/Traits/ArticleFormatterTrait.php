<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Helpers\ArticleHelper;

trait ArticleFormatterTrait
{
    /**
     * Extract common article fields from any API response
     */
    protected function formatArticleData(array $apiData, string $sourceName, string $sourceIdentifier): array
    {
        return [
            'source_name'       => $sourceName,
            'source_identifier' => $sourceIdentifier,
            'article_url'       => $this->extractUrl($apiData, $sourceIdentifier),
            'title'             => $this->extractTitle($apiData, $sourceIdentifier),
            'description'       => $this->extractDescription($apiData, $sourceIdentifier),
            'content'           => $this->extractContent($apiData, $sourceIdentifier),
            'author'            => $this->extractAuthor($apiData, $sourceIdentifier),
            'category'          => $this->extractCategory($apiData, $sourceIdentifier),
            'language'          => $this->extractLanguage($apiData, $sourceIdentifier),
            'image_url'         => $this->extractImageUrl($apiData, $sourceIdentifier),
            'published_at'      => $this->extractPublishedAt($apiData, $sourceIdentifier),
            'fetched_at'        => now(),
        ];
    }

    /**
     * Extract URL based on source
     */
    protected function extractUrl(array $data, string $source): string
    {
        return match($source) {
            'nytimes' => $data['url'] ?? '',
            'newsapi_org' => $data['url'] ?? '',
            'guardian' => $data['webUrl'] ?? '',
            'newsapi_ai' => $data['url'] ?? null,
            default => ''
        };
    }

    /**
     * Extract title based on source
     */
    protected function extractTitle(array $data, string $source): string
    {
        return match($source) {
            'nytimes' => $data['title'] ?? '',
            'newsapi_org' => $data['title'] ?? '',
            'guardian' => $data['webTitle'] ?? '',
            'newsapi_ai' => $data['title'] ?? null,
            default => ''
        };
    }

    /**
     * Extract description based on source
     */
    protected function extractDescription(array $data, string $source): ?string
    {
        return match($source) {
            'nytimes' => $data['abstract'] ?? null,
            'newsapi_org' => $data['description'] ?? null,
            'guardian' => $data['abstract'] ?? null,
            'newsapi_ai' => $data['description'] ?? null,
            default => null
        };
    }

    /**
     * Extract content based on source
     */
    protected function extractContent(array $data, string $source): ?string
    {
        return match($source) {
            'nytimes' => null,
            'newsapi_org' => $data['content'] ?? null,
            'guardian' => null,
            'newsapi_ai' => $data['body'] ?? null,
            default => null
        };
    }

    /**
     * Extract author based on source
     */
    protected function extractAuthor(array $data, string $source)
    {
        return match($source) {
            'nytimes' => ArticleHelper::extractAuthorNyTimes($data['byline'] ?? ''),
            'newsapi_org' => $data['author'] ?? null,
            'guardian' => '',
            'newsapi_ai' => ArticleHelper::extractAuthorsNeswApiAiService($data['authors'] ?? []),
            default => null
        };
    }

    /**
     * Extract category based on source
     */
    protected function extractCategory(array $data, string $source): ?string
    {
        return match($source) {
            'nytimes' => $data['section'] ?? null,
            'newsapi_org' => null,
            'guardian' => $data['sectionName'] ?? null,
            'newsapi_ai' => $data['category'] ?? null,
            default => null
        };
    }

    /**
     * Extract language based on source
     */
    protected function extractLanguage(array $data, string $source): ?string
    {
        return match($source) {
            'nytimes' => 'en',
            'newsapi_ai' => $data['lang'] ?? null,
            default => null
        };
    }

    /**
     * Extract image URL based on source
     */
    protected function extractImageUrl(array $data, string $source): ?string
    {
        return match($source) {
            'nytimes' => ArticleHelper::extractImageUrlNyTimes($data['multimedia'] ?? []),
            'newsapi_org' => $data['urlToImage'] ?? null,
            'guardian' => null,
            'newsapi_ai' => $data['image'] ?? null,
            default => null
        };
    }

    /**
     * Extract published date based on source
     */
    protected function extractPublishedAt(array $data, string $source): ?Carbon
    {
        $dateString = match($source) {
            'nytimes' => $data['published_date'] ?? null,
            'newsapi_org' => $data['publishedAt'] ?? null,
            'guardian' => $data['webPublicationDate'] ?? null,
            'newsapi_ai' => $data['dateTimePub'] ?? null,
            default => null
        };

        return $dateString ? Carbon::parse($dateString) : null;
    }

    /**
     * Process multiple articles from API response
     */
    protected function formatArticlesArray(array $apiArticles, string $sourceName, string $sourceIdentifier): array
    {
        return array_map(fn ($item) => $this->formatArticleData($item, $sourceName, $sourceIdentifier), $apiArticles);
    }
}