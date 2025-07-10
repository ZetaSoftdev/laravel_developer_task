<?php

namespace App\Repositories\ZoomAttendance;

use App\Models\ZoomAttendance;

class ZoomAttendanceRepository implements ZoomAttendanceInterface
{
    private $model;

    public function __construct(ZoomAttendance $model)
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

    public function updateOrCreateByClassAndStudent($classId, $studentId, array $data)
    {
        return $this->builder()->updateOrCreate(
            [
                'zoom_online_class_id' => $classId,
                'student_id' => $studentId
            ],
            $data
        );
    }
} 