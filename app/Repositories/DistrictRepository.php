<?php

namespace App\Repositories;

use App\District;

class DistrictRepository extends Repository
{
    /**
     * DistrictRepository constructor.
     * @param District $model
     */
    public function __construct(District $model)
    {
        $this->model = $model;
    }
}
