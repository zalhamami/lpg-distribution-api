<?php

namespace App\Repositories;

use App\Type;

class TypeRepository extends Repository
{
    /**
     * TypeRepository constructor.
     * @param Type $model
     */
    public function __construct(Type $model)
    {
        $this->model = $model;
    }
}
