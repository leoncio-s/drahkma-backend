<?php

namespace App\Models;

use App\Interfaces\Model;
use App\Validator\StringValidator;

class Categories implements Model
{
    private ?int $id;
    private ?string $description;
    private ?int $user;


    public function __construct(int $id = null, string $description = null, int $user = null) {
        $this->setId($id);
        $this->setDescription($description);
        $this->setUser($user);
    }


    public function getId() : int {
        return $this->id;
    }

    public function getDescription() : string{
        return $this->description;
    }

    public function getUserId(): int{
        return $this->user;
    }

    public function setId(?int $id) : void {
        $this->id = ($id > 0) ? $id : null;
    }

    public function setDescription(?string $value) : void{
        $this->description = (StringValidator::descrValidate($value) && strlen($value) >= 3 && strlen($value) <= 20) ? $value : "";
    }

    public function setUser(?int $user): void{
        $this->user = $user;
    }


    public function toJson(): string
    {
        return json_encode([
            'decription' => $this->getDescription(),
            'user' => $this->getUserId()
        ]);
    }

    public function toArray(): array
    {
        return ['decription' => $this->getDescription(),
            'user' => $this->getUserId()];
    }

    public function toObject(array $data): Model
    {
        $id = (isset($data['id']) && is_int($data['id']))? $data['id'] : null;
        $description = (isset($data['description']))? $data['description'] : null;
        $user = (isset($data['user']) && gettype($data['user']) == "integer")? $data['user'] : null;

        $this->setId($id);
        $this->setDescription($description);
        $this->setUser($user);
        
        return $this;
        
    }

    public static function validate(array $data):array{
        return [];
    }
}
