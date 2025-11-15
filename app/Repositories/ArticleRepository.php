<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Article;
use Illuminate\Support\Facades\Log;
use App\Interfaces\SourceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ArticleRepository implements SourceRepositoryInterface
{
     protected Article $model;

    public function __construct(Article $article)
    {
        $this->model = $article;
    }

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

            $article = $this->model::updateOrCreate(
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

    public function getFilteredArticles(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        // Search in title, description, content
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        //flter by author
        if (!empty($filters['author'])) {
            $query->where('author', $filters['author']);
        }

        // Filter by source
        if (!empty($filters['source'])) {
            $query->where('source_identifier', $filters['source']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->where('published_at', '>=', Carbon::parse($filters['date_from']));
        }
        if (!empty($filters['date_to'])) {
            $query->where('published_at', '<=', Carbon::parse($filters['date_to']));
        }

        // Sorting - use validated default from request or fallback
        $sort = $filters['sort'] ?? 'desc';
        $query->orderBy('published_at', $sort);

        return $query->paginate($perPage)->appends($filters);
    }
}