<?php

namespace App\Http\Controllers;

use App\Repositories\ProvinceRepository;
use Illuminate\Http\Request;

class ProvinceController extends ApiController
{
    /**
     * ProvinceController constructor.
     * @param ProvinceRepository $repo
     */
    public function __construct(ProvinceRepository $repo) {
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
            'state_id' => ['nullable', 'integer'],
            'country_id' => ['required', 'string', 'max:3', 'exists:countries,id'],
            'iso_id' => ['nullable', 'integer'],
            'name' => ['required', 'string'],
            'timezone' => ['nullable', 'timezone'],
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
}
