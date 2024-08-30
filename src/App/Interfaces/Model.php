<?php

namespace App\Interfaces;

interface Model{

    public function toJson() : string;
    public function toObject(array $data): Model;
}