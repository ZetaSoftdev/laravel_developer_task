<?php

namespace App\Repositories\ZoomOnlineClass;

interface ZoomOnlineClassInterface
{
    public function builder();
    public function all();
    public function findById($id, $columns = ['*'], $relations = []);
    public function create(array $data);
    public function update($id, array $data);
    public function deleteById($id);
} 