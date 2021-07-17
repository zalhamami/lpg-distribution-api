<?php

namespace App\Repositories;

use App\City;

class CityRepository extends Repository
{
    /**
     * CountryRepository constructor.
     * @param City $model
     */
    public function __construct(City $model)
    {
        $this->model = $model;
    }
}
