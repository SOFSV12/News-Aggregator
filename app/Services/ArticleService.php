<?php

namespace App\Services;

use App\Interfaces\SourceRepositoryInterface;
use App\Services\Sources\GuardianService;
use App\Services\Sources\NewsApiAiService;
use App\Services\Sources\NewsApiOrgService;
use App\Services\Sources\NyTimesService;
use Illuminate\Support\Facades\Log;

class ArticleService
{
    protected $repository;
    protected $sources;

    public function __construct(
        SourceRepositoryInterface $repository,
        NyTimesService $nyTimesService,
        GuardianService $guardianService,
        NewsApiAiService $newsApiAiService,
        NewsApiOrgService $newsApiOrgService
    ) {
        $this->repository = $repository;
        $this->sources = [
            $nyTimesService,  
            $guardianService, 
            $newsApiOrgService, 
            $newsApiAiService, 
        ];
    }

    public function fetchFromAllSources(): void
    {
        foreach ($this->sources as $source) {
            try {
                $articles = $source->fetchArticles();
                $savedCount = 0;

                foreach ($articles as $article) {
                    if ($this->repository->saveArticle($article)) {
                        $savedCount++;
                    }
                }

                Log::info(get_class($source) . ' fetched ' . count($articles) . ' articles, saved ' . $savedCount . ' successfully.');
            } catch (\Exception $e) {
                Log::error('Failed fetching from ' . get_class($source) . ': ' . $e->getMessage());
            }
        }
    }
}