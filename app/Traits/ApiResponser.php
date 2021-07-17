<?php

namespace App\Traits;

trait ApiResponser
{
    /**
     * @param $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data, $code = 200)
    {
        return response()->json($data, $code);
    }

    /**
     * @param $message
     * @param $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message, $code)
    {
        return response()->json([
            'code' => $code,
            'message' => $message
        ], $code);
    }

    /**
     * @param $collection
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function collectionData($collection, $code = 200)
    {
        return $this->successResponse($collection, $code);
    }

    /**
     * @param $model
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function singleData($model, $code = 200)
    {
        return $this->successResponse($model, $code);
    }

    /**
     * @param array $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    protected function deleteMessage($data = [], $code = 200)
    {
        return $this->successResponse([
            'message' => 'Delete Success',
            'data' => $data,
        ], $code);
    }
}
