<?php

namespace App\Categories;

use App\Interfaces\Model;
use App\Validators\StringValidator;

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
        $this->description = ($value != null && StringValidator::descrValidate($value) && strlen($value) >= 3 && strlen($value) <= 30) ? strtoupper($value) : "";
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
        return [
            'id'    => $this->getId(),
            'description' => $this->getDescription(),
            // 'user' => $this->getUserId()];
        ];
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
        $description = (isset($data['description']))? strtoupper($data['description']) : null;
        $user = (isset($data['user']) && is_int($data['user']))? $data['user'] : null;

        $error['description'] = [];

        if($description == null || $description == "") array_push($error['description'], "This field is required");
        elseif(!(strlen($description) >=3 && strlen($description) <= 30)) array_push($error['description'], "Min. lenght 3 and max. lenght 30");
        elseif(!StringValidator::descrValidate($description)) array_push($error['description'], "Invalid value for field");

        foreach($error as $k => $v){
            if(count($error[$k]) <= 0) unset($error[$k]);
        }

        if(count($error, COUNT_RECURSIVE) > 0) return ['errors' => $error, "data" => ['description' => $description, "user" => $user]];
        else return ["data" => ['description' => $description, "user" => $user]];

    }
}
