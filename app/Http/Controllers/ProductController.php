<?php

namespace App\Http\Controllers;

use App\Repositories\ProductRepository;
use App\Services\FileService;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    /**
     * @var FileService
     */
    private $fileService;

    /**
     * ProductController constructor.
     * @param ProductRepository $repo
     */
    public function __construct(ProductRepository $repo) {
        $this->repo = $repo;
        $this->fileService = new FileService('gcs');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->repo->getAll();
        return $this->collectionData($data);
    }

    /**
     * @param Request $request
     */
    private function requestValidation(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'image' => ['nullable', 'image', 'max:2048'],
            'price' => ['required', 'numeric'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id,deleted_at,NULL'],
        ]);
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->requestValidation($request);

        $payload = $request->all();
        if ($request['image']) {
            $image = $this->fileService->saveToStorage($request['image'], 'products/');
            $payload['image_url'] = $image['url'];
        }

        $response = $this->repo->create($payload);
        return $this->singleData($response, 201);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $data = $this->repo->getById($id);
        return $this->singleData($data);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $this->requestValidation($request);

        $product = $this->repo->getById($id);

        $payload = $request->all();
        if ($request['image']) {
            $image = $this->fileService->saveToStorage($request['image'], 'products/');
            $payload['image_url'] = $image['url'];
        } else {
            $payload['image_url'] = $product->image_url;
        }

        $response = $this->repo->update($id, $payload);
        return $this->singleData($response);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        $data = $this->repo->delete($id);
        return $this->deleteMessage($data);
    }
}
