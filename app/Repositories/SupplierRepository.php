<?php

namespace App\Repositories;

use App\Supplier;

class SupplierRepository extends Repository
{
    /**
     * SupplierRepository constructor.
     * @param Supplier $model
     */
    public function __construct(Supplier $model)
    {
        $this->model = $model;
    }
}
