<?php

namespace App\Interfaces;

use App\Interfaces\Model;

interface ServiceInterface2{
    public function __construct(RepositoryInterface $repository);
    
    public function create(array $data) : Model | null;
    // public function read(string $id) : Model | array | HasOne | HasMany | Paginator | null ;
    public function update(array $data) : Model | null;
    public function delete(Model $data) : int | null;
}