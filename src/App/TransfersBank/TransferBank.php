<?php

namespace App\TransfersBank;

use App\BankAccounts\BankAccounts;
use App\TransfersBank\TransferBankEnum;
use App\Interfaces\Model;
use App\Users\User;
use App\Validators\StringValidator;
use Exception;

class TransferBank implements Model
{
   
    private ?int $id;
    private ?int $user;
    private ?TransferBankEnum $type;
    private ?string $description;
    private ?BankAccounts $bank_account;


    public function __construct(int $id = null, TransferBankEnum $type=null, string $description = null, BankAccounts $bankAccounts = null, User $user=null) {
        $this->setId($id);
        $this->setType($type);
        $this->setDescription($description);
        $this->setBankAccount($bankAccounts);
    }

    public function setId(?int $value): void{
        $this->id = ($value > 0) ? $value : null;
    }
    public function setType(?TransferBankEnum $value): void{
        $this->type = $value;
    }
    public function setDescription(?string $value): void{
        $this->description = ($value != null && StringValidator::descrValidate($value) && strlen($value) > 3 && strlen($value) <= 250) ? $value : "";
    }
    public function setBankAccount(?BankAccounts $value): void{
        $this->bank_account = ($value instanceof BankAccounts) ? $value : null;
    }
    public function setUser(?int $user){
        $this->user = $user;
    }

    public function getId(): ?int{
        return $this->id;
    }
    public function getType(): ?string{
        return $this->type->value;
    }
    public function getDescription(): ?string{
        return $this->description;
    }
    public function getBankAccount(): ?BankAccounts {
        return $this->bank_account;
    } 
    public function getUserId():?int{
        return $this->user;
    }


    public function toJson(): string
    {
        return json_encode([
            'id' => $this->getId(),
            'type' => $this->getType(),
            'description' => $this->getDescription(),
            'bank_account' => $this->getBankAccount()->toJson(),
        ]);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType(),
            'description' => $this->getDescription(),
            'bank_account' => $this->getBankAccount()->toArray(),
        ];
    }

    public function toObject(array $data): Model
    {
        // var_dump($data);
        $id = (isset($data['id']) && is_int($data['id'])) ? $data['id'] : null;
        $type = (isset($data['type'])) ? TransferBankEnum::tryFrom($data['type']) : null;
        $description = (isset($data['description'])) ? $data['description'] : null;
        $bank_account = (isset($data['bank_account']) && is_array($data['bank_account'])) ? (new BankAccounts())->toObject($data['bank_account']) : null;
        $user = (isset($data['user']) && is_int($data['user'])) ? $data['user'] : null;


        $this->setId($id);
        $this->setType($type);
        $this->setDescription($description);
        $this->setBankAccount($bank_account);
        $this->setUser($user);
        
        return $this;
    }

    public static function validate(array $data, bool $update=false): array
    {
        $type = (isset($data['type'])) ? TransferBankEnum::tryFrom($data['value']) : null;
        $description = (isset($data['description'])) ? $data['description'] : null;
        $user = (isset($data['user']))? $data['user'] : null;
        $bank_account = (isset($data['bank_account']) ? $data['bank_account'] : null);

        $errors['type'] = [];
        $errors['description'] = [];
        $errors['bank_account'] = [];

        if($type == null) array_push($errors['type'], "Field is required");
        
        if($description == null || $description == "") array_push($errors['description'], "This field is required");
        elseif(!(strlen($description) >=5 && strlen($description) <= 250)) array_push($errors['description'], "Min. lenght is 5 and max. 250");
        elseif(!StringValidator::descrValidate($description)) array_push($errors['description'], "Value invalid");

        if($bank_account == null) array_push($errors['bank_account'], "This field is required");
        elseif($bank_account < 1)array_push($errors['bank_account'], "Value invalid");

        if($update){
            $errors['id'] = [];
            if(!isset($data['id'])){
                array_push($errors['id'], "This field is required");
            }
            if(isset($data['id']) && $data['id'] < 1){
                array_push($errors['id'], "Invalid value");
            }
        }

        foreach($errors as $k=>$v){
            if(count($errors[$k])>0) unset($errors[$k]);
        }

        if(count($errors, COUNT_RECURSIVE) > 0) return ['errors' => $errors, "data" => [
            "description" => $description,
            "type" => $type->value
        ]];
        return ["data" => [
            "description" => $description,
            "type" => $type->value,
            "user" => $user
        ]];
    }
}
