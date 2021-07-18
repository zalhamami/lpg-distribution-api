<?php

namespace App\Http\Controllers;

use App\Repositories\OrderPaymentRepository;

class OrderPaymentController extends ApiController
{
    /**
     * OrderPaymentController constructor.
     * @param OrderPaymentRepository $repo
     */
    public function __construct(OrderPaymentRepository $repo) {
        $this->repo = $repo;
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
}
