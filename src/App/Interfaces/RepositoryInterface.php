<?php

namespace App\Interfaces;

use App\Database\Databases;

interface RepositoryInterface{
    public function __construct(Databases $databases);
    public static function get(int $id): Model | null;
    public static function save(array $data):Model | null;
    public static function update(array $data):Model | null;
    public static function delete($data):bool;
}
