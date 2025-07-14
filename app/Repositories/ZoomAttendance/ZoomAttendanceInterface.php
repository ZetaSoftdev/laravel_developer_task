<?php

namespace App\Repositories\ZoomAttendance;

interface ZoomAttendanceInterface
{
    public function builder();
    public function all();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function deleteById($id);
    public function updateOrCreateByClassAndStudent($classId, $studentId, array $data);
} 