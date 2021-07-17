<?php

namespace App\Repositories;

use App\Province;

class ProvinceRepository extends Repository
{
    /**
     * ProvinceRepository constructor.
     * @param Province $model
     */
    public function __construct(Province $model)
    {
        $this->model = $model;
    }
}
