<?php

namespace App\Http\Controllers;

use App\Repositories\CountryRepository;
use App\Repositories\TypeRepository;
use Illuminate\Http\Request;

class CountryController extends ApiController
{
    /**
     * CountryController constructor.
     * @param CountryRepository $repo
     */
    public function __construct(CountryRepository $repo) {
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
            'id' => ['required', 'string', 'max:3'],
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
        $request->validate([
            'id' => 'unique:countries'
        ]);
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
