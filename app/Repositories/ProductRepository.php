<?php

namespace App\Repositories;

use App\Product;

class ProductRepository extends Repository
{
    /**
     * ProductRepository constructor.
     * @param Product $model
     */
    public function __construct(Product $model)
    {
        $this->model = $model;
    }
}
