<?php

namespace App\Http\Controllers;

use App\Repositories\CityRepository;
use Illuminate\Http\Request;

class CityController extends ApiController
{
    /**
     * CityController constructor.
     * @param CityRepository $repo
     */
    public function __construct(CityRepository $repo) {
        $this->repo = $repo;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $data = $this->repo->getAll();
        return $this->collectionData($data);
    }

    private function requestValidation(Request $request)
    {
        $request->validate([
            'province_id' => ['required', 'integer', 'exists:provinces,id'],
            'iso_id' => ['nullable', 'integer'],
            'name' => ['required', 'string'],
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        $this->requestValidation($request);
        return $this->repo->create($request->all());
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
     * @param $id
     */
    public function update(Request $request, $id)
    {
        $this->requestValidation($request);
        return $this->repo->update($id, $request->all());
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
