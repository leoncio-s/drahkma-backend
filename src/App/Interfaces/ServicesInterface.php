<?php

namespace App\Interfaces;

use App\Interfaces\Model;

interface ServicesInterface{
    // public function __construct(RepositoryInterface $repository);
    
    public function create(array $data) : Model | array | null;
    public function update(array $data) : Model | array | null;
    public function delete(array $data) : bool | int | null;
}