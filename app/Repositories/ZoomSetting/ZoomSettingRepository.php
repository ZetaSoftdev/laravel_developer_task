<?php

namespace App\Repositories\ZoomSetting;

use App\Models\ZoomSetting;
use Illuminate\Support\Facades\Auth;

class ZoomSettingRepository implements ZoomSettingInterface
{
    private $model;

    public function __construct(ZoomSetting $model)
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

    public function findById($id)
    {
        return $this->builder()->findOrFail($id);
    }

    public function create(array $data)
    {
        $data['school_id'] = Auth::user()->school_id;
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