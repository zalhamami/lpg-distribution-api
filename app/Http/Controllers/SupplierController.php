<?php

namespace App\Http\Controllers;

use App\Repositories\SupplierRepository;
use App\Role;
use Illuminate\Http\Request;
use Propaganistas\LaravelPhone\PhoneNumber;

class SupplierController extends ApiController
{
    /**
     * SupplierController constructor.
     * @param SupplierRepository $repo
     */
    public function __construct(SupplierRepository $repo) {
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
     */
    private function requestValidation(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'contact' => ['required', 'phone:ID'],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getPayload(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = $request->user()->id;
        $data['contact'] = PhoneNumber::make($request['contact'], 'ID')->formatE164();
        return $data;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $this->requestValidation($request);
        $payload = $this->getPayload($request);
        $response = $this->repo->create($payload);
        return $this->singleData($response, 201);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeAddress(Request $request, int $id)
    {
        $request->validate([
            'address' => ['required', 'string'],
            'district_id' => ['required', 'integer', 'exists:districts,id'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'postal_code' => ['required', 'string'],
        ]);

        $supplier = $this->repo->getById($id);
        $user = $request->user();
        if (!$this->isOwner($supplier, $user)) {
            return $this->errorResponse('You are not allowed to update this resource', 403);
        }

        if ($supplier->address) {
            $supplier->address()->update($request->all());
        } else {
            $supplier->address()->create($request->all());
        }
        $supplier->refresh();

        return $this->singleData($supplier, 201);
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

        $supplier = $this->repo->getById($id);
        $user = $request->user();
        if (!$this->isOwner($supplier, $user)) {
            return $this->errorResponse('You are not allowed to update this resource', 403);
        }

        $payload = $this->getPayload($request);
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
