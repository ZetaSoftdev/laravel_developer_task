<?php

namespace App\Repositories\ZoomOnlineClass;

use App\Models\ZoomOnlineClass;
use Illuminate\Support\Facades\Auth;

class ZoomOnlineClassRepository implements ZoomOnlineClassInterface
{
    private $model;

    public function __construct(ZoomOnlineClass $model)
    {
        $this->model = $model;
    }

    public function builder()
    {
        return $this->model->newQuery();
    }

    public function all()
    {
        return $this->builder()->get();
    }

    public function findById($id, $columns = ['*'], $relations = [])
    {
        $query = $this->builder()->select($columns);
        
        if (!empty($relations)) {
            $query->with($relations);
        }
        
        return $query->findOrFail($id);
    }

    public function create(array $data)
    {
        $data['school_id'] = Auth::user()->school_id;
        $data['teacher_id'] = Auth::id();
        return $this->builder()->create($data);
    }

    public function update($id, array $data)
    {
        $model = $this->findById($id);
        $model->update($data);
        return $model;
    }

    public function deleteById($id)
    {
        return $this->findById($id)->delete();
    }
} 