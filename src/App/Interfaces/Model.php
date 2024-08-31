<?php

namespace App\Interfaces;

interface Model{

    public function toJson() : string;
    public function toObject(array $data): Model;
    public function toArray(): array;
    public static function validate(array $data): array;
}