<?php

namespace App\Traits;

use Illuminate\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;


trait ResponseTraits
{
    protected function responseJson(mixed $data, mixed $pagination = null, string $message = '', int $responseCode = Response::HTTP_OK, bool $status = true): JsonResponse
    {
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,
            'pagination' => $pagination,
            'statusCode' => $responseCode,
        ], $responseCode);
    }

}
