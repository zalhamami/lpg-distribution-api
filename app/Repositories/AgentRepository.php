<?php

namespace App\Repositories;

use App\Agent;

class AgentRepository extends Repository
{
    /**
     * AgentRepository constructor.
     * @param Agent $model
     */
    public function __construct(Agent $model)
    {
        $this->model = $model;
    }
}
