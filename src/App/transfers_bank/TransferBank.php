<?php

namespace App\Models;

use App\Enums\TransferBankEnum;
use App\Interfaces\Model;
use App\Users\User;
use App\Validator\StringValidator;
use Exception;

class TransferBank implements Model
{
   
    private ?int $id;
    private ?User $user;
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
        $this->description = (StringValidator::descrValidate($value) && strlen($value) > 3 && strlen($value) <= 250) ? $value : throw new Exception("Invalide description value");
    }
    public function setBankAccount(?BankAccounts $value): void{
        $this->bank_account = ($value instanceof BankAccounts) ? $value : throw new Exception("Invalid bank account value");
    }
    public function setUser(?User $user){
        $this->user = $user;
    }

    public function getId(): ?int{
        return $this->id;
    }
    public function getType(): ?string{
        return $this->type->name;
    }
    public function getDescription(): ?string{
        return $this->description;
    }
    public function getBankAccount(): ?string {
        return $this->bank_account->getBankName();
    } 
    public function getUserId():?int{
        return $this->user->getId();
    }


    public function toJson(): string
    {
        return json_encode([
            'id' => $this->getId(),
            'type' => $this->getType(),
            'user' => $this->getUserId(),
            'description' => $this->getDescription(),
            'bank_account' => $this->getBankAccount(),
        ]);
    }

    public function toObject(array $data): Model
    {
        $id = (isset($data['id']) && is_int($data['id'])) ? $data['id'] : null;
        $type = (isset($data['type'])) ? TransferBankEnum::tryFrom($data['value']) : null;
        $description = (isset($data['description'])) ? $data['description'] : null;
        $bank_account = (isset($data['bank_account']) && $data['bank_account'] instanceof BankAccounts) ? $data['bank_account'] : null;
        $user = (isset($data['user']) && $data['user'] instanceof User) ? $data['user'] : null;


        $this->setId($id);
        $this->setType($type);
        $this->setDescription($description);
        $this->setBankAccount($bank_account);
        $this->setUser($user);
        
        return $this;
    }

}
