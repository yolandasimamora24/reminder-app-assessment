<?php

namespace App\Traits;

use Illuminate\Http\Response;
use App\Enums\CommonStatusEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

trait CommonResponseTrait
{
    public function successResponse(string $message, string $status, array $data = [], int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()
            ->json([
                'message' => $message,
                'success' => true,
                'status' => $status,
                'data' => $data
            ], $statusCode);
    }

    public function paginatedResponse(string $message, string $status, LengthAwarePaginator $data, int $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()
            ->json([
                'message' => $message,
                'success' => true,
                'status' => $status,
                'data' => $data->items(),
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'query_string' => http_build_query(request()->except('per_page', 'page')),
                    'has_more_pages' => $data->hasMorePages(),
                ],
            ], $statusCode);
    }

    public function errorResponse(string $message, string $status, array $data = [], int $statusCode = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return response()
            ->json([
                'message' => $message,
                'success' => false,
                'status' => $status,
                'data' => $data
            ], $statusCode);
    }

    public function validationErrorResponse(string $message = 'Validation error.', string $status = CommonStatusEnum::VALIDATION_ERROR, array $errors = [], array $data = []): JsonResponse
    {
        return response()
            ->json([
                'message' => $message,
                'success' => false,
                'status' => $status,
                'data' => $data,
                'errors' => $errors
            ], 422);
    }
}
