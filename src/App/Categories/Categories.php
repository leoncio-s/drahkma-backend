<?php

namespace App\Models;

use App\Interfaces\Model;
use App\Users\User;
use App\Validator\StringValidator;
use Exception;

class Categories implements Model
{
    private int $id;
    private string $description;
    private User $user;


    public function __construct(int $id = null, string $description = null, User $user = null) {
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
        return $this->user->getId();
    }

    public function setId(int $id) : void {
        $this->id = ($id > 0) ? $id : throw new Exception("invalid value for id");
    }

    public function setDescription(string $value) : void{
        $this->description = (StringValidator::descrValidate($value) && strlen($value) >= 3 && strlen($value) <= 20) ? $value : throw new Exception("invalid description value");
    }

    public function setUser(User $user): void{
        $this->user = ($user instanceof User) ? $user : throw new Exception("invalid user value");
    }


    public function toJson(): string
    {
        return json_encode([
            'decription' => $this->getDescription(),
            'user' => $this->getUserId()
        ]);
    }

    public function toObject(array $data): Model
    {
        $id = (isset($data['id']) && is_int($data['id']))? $data['id'] : null;
        $description = (isset($data['description']))? $data['description'] : null;
        $user = (isset($data['user']) && $data['user'] instanceof User)? $data['user'] : null;

        $this->setId($id);
        $this->setDescription($description);
        $this->setUser($user);
        
        return $this;
        
    }
}
