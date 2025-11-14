<?php

namespace App\Repositories;

use App\Interfaces\SourceRepositoryInterface;
use App\Models\Article;
use Illuminate\Support\Facades\Log;

class ArticleRepository implements SourceRepositoryInterface
{
    /**
     * Save an article using the provided data array.
     *
     * @param array $data
     * @return bool True on success, false on failure
     */
    public function saveArticle(array $data): bool
    {
        try {
            if (empty($data['article_url']) || empty($data['title'])) {
                return false;
            }

            $article = Article::updateOrCreate(
                ['article_url' => $data['article_url']],
                [
                    'source_name' => $data['source_name'] ?? null,
                    'source_identifier' => $data['source_identifier'] ?? null,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                    'content' => $data['content'] ?? null,
                    'author' => $data['author'] ?? null,
                    'category' => $data['category'] ?? null,
                    'language' => $data['language'] ?? null,
                    'image_url' => $data['image_url'] ?? null,
                    'published_at' => $data['published_at'] ?? null,
                    'fetched_at' => $data['fetched_at'] ?? now(),
                ]
            );

            return (bool) $article;

        } catch (\Exception $e) {
            Log::error('Failed to save article: ' . $e->getMessage());
            return false;
        }
    }
}