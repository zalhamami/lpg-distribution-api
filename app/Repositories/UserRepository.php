<?php

namespace App\Repositories;

use App\User;

class UserRepository extends Repository
{
    /**
     * TypeRepository constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }
}
