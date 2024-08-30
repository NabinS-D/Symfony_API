<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait ResponseTrait
{

    protected function successResponse(string $message,int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        return $this->json([
            'status' => 'true',
            'message' => $message,    
        ], $status);
    }

    
    protected function successResponseWithData(string $message = null, $data = null,  int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        return $this->json([
            'status' => 'true',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function errorResponse(string $message, int $status = JsonResponse::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->json([
            'status' => 'false',
            'message' => $message,
        ], $status);
    }

    protected function errorResponseWithErrors( $errors = null, int $status = JsonResponse::HTTP_BAD_REQUEST): JsonResponse
    {
        return $this->json([
            'status' => 'false',
            'message' => 'Invalid Request',
            'errors' => $errors,
        ], $status);
    }
}
