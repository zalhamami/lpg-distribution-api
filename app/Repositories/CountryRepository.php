<?php

namespace App\Repositories;

use App\Country;

class CountryRepository extends Repository
{
    /**
     * CountryRepository constructor.
     * @param Country $model
     */
    public function __construct(Country $model)
    {
        $this->model = $model;
    }
}
