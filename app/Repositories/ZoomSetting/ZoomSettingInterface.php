<?php

namespace App\Repositories\ZoomSetting;

interface ZoomSettingInterface
{
    public function builder();
    public function all();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function deleteById($id);
} 