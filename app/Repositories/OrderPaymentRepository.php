<?php

namespace App\Repositories;

use App\OrderPayment;

class OrderPaymentRepository extends Repository
{
    /**
     * OrderRepository constructor.
     * @param OrderPayment $model
     */
    public function __construct(OrderPayment $model)
    {
        $this->model = $model;
    }
}
