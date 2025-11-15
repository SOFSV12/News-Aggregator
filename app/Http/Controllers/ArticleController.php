<?php

namespace App\Http\Controllers;

use App\Helpers\PaginationHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Traits\ResponseTraits;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ArticleFilterRequest;

class ArticleController extends Controller
{
    use ResponseTraits;

    public function __construct(private ArticleService $articleService)
    {
    }

    public function index(ArticleFilterRequest $request): JsonResponse
    {
        try {
            // Get validated filters
            $filters = $request->validated();

            $articles = $this->articleService->getFilteredArticles($filters);

            return $this->responseJson(data: $articles->items(),pagination: PaginationHelper::formatPagination($articles), message: 'Articles retrieved successfully', responseCode: Response::HTTP_OK);

        } catch (\Exception $e) {
            return $this->responseJson( data: [], pagination: null, message: 'Error retrieving articles: ' . $e->getMessage(), responseCode: Response::HTTP_INTERNAL_SERVER_ERROR, status: false);
        }
    }
}
