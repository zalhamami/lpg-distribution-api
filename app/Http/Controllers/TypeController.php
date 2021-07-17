<?php

namespace App\Http\Controllers;

use App\Repositories\TypeRepository;
use Illuminate\Http\Request;

class TypeController extends ApiController
{
    /**
     * TypeController constructor.
     * @param TypeRepository $repo
     */
    public function __construct(TypeRepository $repo) {
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

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
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
        return $this->repo->update($id, $request->all());
    }
}
