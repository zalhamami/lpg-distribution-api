<?php

namespace App\Repositories;

use App\Traits\ApiRequester;
use Illuminate\Database\Eloquent\Model;

class Repository
{
    use ApiRequester;

    /**
     * @var Model
     */
    protected $model;

    public function getAll($query = NULL)
    {
        if (!$this->model) {
            return;
        }

        $request = $this->getRequest();
        $collection = $this->model;
        if ($query) {
            $collection = $query;
        }

        if ($request['sort_by']) {
            if ($request['sort_direction'] == 'desc') {
                $collection = $collection->orderByDesc($request['sort_by']);
            } else {
                $collection = $collection->orderBy($request['sort_by']);
            }
        }
        if ($request['keyword']) {
            $collection = $collection->where('name', 'like', '%' . $request['keyword'] . '%');
        }

        $data = $collection->paginate($request['per_page']);
        return $data;
    }

    public function getAllWithFilters(array $filters = [], $or = false)
    {
        $query = $this->model;
        for ($i = 0; $i < count($filters); $i++) {
            if ($i === 0) {
                $query = $query->where($filters[$i]['field'], $filters[$i]['value']);
            } else {
                if ($or === true) {
                    $query = $query->orWhere($filters[$i]['field'], $filters[$i]['value']);
                } else {
                    $query = $query->where($filters[$i]['field'], $filters[$i]['value']);
                }
            }
        }
        return $this->getAll($query);
    }

    public function getAllWithDetails()
    {
        $query = $this->model->details();
        return $this->getAll($query);
    }

    public function getById($id)
    {
        if (!$this->model) {
            return;
        }
        $data = $this->model->details()->findOrFail($id);
        return $data;
    }

    public function getByField($field, $value, $comparator = '=', $latest = false)
    {
        if (!$this->model) {
            return null;
        }
        $data = $this->model->details()->where($field, $comparator, $value);
        if ($latest) {
            $data = $data->orderByDesc('id');
        }
        return $data->firstOrFail();
    }

    public function create($payload)
    {
        return $this->model->create($payload);
    }

    public function update($id, $payload)
    {
        $data = $this->getById($id);
        $data->update($payload);
        return $data;
    }

    public function delete($id)
    {
        $data = $this->getById($id);
        $data->delete();
        return $data;
    }

    public function forceDelete($id)
    {
        $data = $this->getById($id);
        $data->forceDelete();
        return $data;
    }

    public function restore(int $id)
    {
        return $this->model->withTrashed()->findOrFail($id)->restore();
    }
}
