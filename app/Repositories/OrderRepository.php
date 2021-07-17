<?php

namespace App\Repositories;

use App\Order;

class OrderRepository extends Repository
{
    /**
     * OrderRepository constructor.
     * @param Order $model
     */
    public function __construct(Order $model)
    {
        $this->model = $model;
    }

    /**
     * @param int $targetId
     * @param string $type
     * @param string $level
     */
    public function getAllUserOrders(int $targetId, string $type, string $level)
    {
        $levelType = Order::TYPE_USER;
        if (strtolower($level) === 'agent') {
            $levelType = Order::TYPE_AGENT;
        }
        if (strtolower($level) === 'supplier') {
            $levelType = Order::TYPE_SUPPLIER;
        }

        $query = $this->model->where("{$type}_id", $targetId)->where("{$type}_type", $levelType);
        return $this->getAll($query);
    }
}
