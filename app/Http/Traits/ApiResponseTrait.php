<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Return success response
     *
     * @param mixed $data
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function successResponse($data = null, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return error response
     *
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return JsonResponse
     */
    protected function errorResponse(string $message = 'Error', int $code = 400, $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }

    /**
     * Return paginated response
     *
     * @param array $data
     * @param int $total
     * @param int $currentPage
     * @param int $perPage
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    protected function paginatedResponse(array $items, int $total, int $currentPage, int $perPage, string $message = 'Success', int $code = 200): JsonResponse
    {
        $lastPage = max((int) ceil($total / $perPage), 1);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'items' => $items,
                'pagination' => [
                    'total' => $total,
                    'current_page' => $currentPage,
                    'per_page' => $perPage,
                    'last_page' => $lastPage,
                ],
            ],
        ], $code);
    }

    /**
     * Return validation error response
     *
     * @param array $errors
     * @param string $message
     * @return JsonResponse
     */
    protected function validationErrorResponse(array $errors, string $message = 'Validation error'): JsonResponse
    {
        return $this->errorResponse($message, 422, $errors);
    }
}
