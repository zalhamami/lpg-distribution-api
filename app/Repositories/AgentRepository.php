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

    public function getAll($query = NULL)
    {
        $request = request();
        if (@$request['city_id']) {
            $query = $this->model->whereHas('address.district', function ($district) use (&$request) {
                $district->where('city_id', (int)$request['city_id']);
            });
        }
        if (@$request['district_id']) {
            $query = $this->model->whereHas('address', function ($address) use (&$request) {
                $address->where('district_id', (int)$request['district_id']);
            });
        }
        return parent::getAll($query);
    }
}
