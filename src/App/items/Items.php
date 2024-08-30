<?php

namespace App\Models;

use App\Interfaces\Model;
use App\Users\User;
use App\Validator\StringValidator;
use DateTime;
use Exception;

class Items implements Model
{
    private int $id;
    private User $user;
    private string $description;
    private bool $expense;
    private float $value;
    private DateTime $date;
    private Categories $category;
    private Cards $card;
    private TransferBank $transfer_bank;


    public function __construct(
        int $id = null,
        User $user = null,
        string $description = null,
        bool $expense = null,
        float $value = null,
        string $date = null,
        Categories $category = null,
        Cards $card = null,
        TransferBank $transfer_bank = null) {
        $this->setId($id);
        $this->setUser($user);
        $this->setDescription($description);
        $this->setExpense($expense);
        $this->setValue($value);
        $this->setDate($date);
        $this->setCategory($category);
        $this->setCard($card);
        $this->setTransferbank($transfer_bank);
    }



    public function getId(): int{
        return $this->id;
    }
    public function getUserId():int{
        return $this->user->getId();
    }
    public function getDescription():string{
        return $this->description;
    }
    public function getExpense(): bool{
        return $this->expense;
    }
    public function getValue():float{
        return $this->value;
    }
    public function getDate():DateTime{
        return $this->date;
    }
    public function getCategoryDescription():string{
        return $this->category->getDescription();
    }
    public function getCardsBrand():string{
        return $this->card->getBrand();
    }
    public function getTransferbank():TransferBank{
        return $this->transfer_bank;
    }

    public function setId(int $value): void{
        $this->id = ($value > 0) ? $value : throw new Exception("invalid id value");
    }
    public function setUser(User $value):void{
        $this->user = ($value instanceof User) ? $value : throw new Exception("invalid user value");
    }
    public function setDescription(string $value):void{
        $this->description = (StringValidator::descrValidate($value) && strlen($value) > 3 && strlen($value) <= 100) ? $value :  throw new Exception("invalid description value");
    }
    public function setExpense(bool $value): void{
        $this->expense = $value;
    }
    public function setValue(float $value):void{
        $this->value = $value;
    }
    public function setDate(string $value):void{
        try{
            $this->date = new DateTime($value);
        }catch(Exception){
            throw new Exception("Invalid date value");
        }
    }
    public function setCategory(Categories $value):void{
        $this->category = ($value instanceof Categories) ? $value : throw new Exception("invalid Category value");
    }
    public function setCard(Cards $value):void{
        $this->card = ($value instanceof Cards) ? $value : throw new Exception("invalid card value");
    }
    public function setTransferbank(TransferBank $value):void{
        $this->transfer_bank = ($value instanceof TransferBank) ? $value : throw new Exception("invalid transfer bank value");
    }


    public function toJson(): string
    {
        return json_encode(
            [
                'id' => $this->getId(),
                'user' => $this->getUserId(),
                'description' => $this->getDescription(),
                'expense' => $this->getExpense(),
                'value' => $this->getValue(),
                'date' => $this->getDate(),
                'category' => $this->getCategoryDescription(),
                'card' => $this->getCardsBrand(),
                'transfer_bank' => $this->getTransferbank()->toJson()
            ]
        );
    }

    public function toObject(array $data): Model
    {
        $id = (isset($data['id']) && is_int($data['id'])) ? $data['id'] : null;
        $user = (isset($data['user']) && $data['user'] instanceof User) ? $data['user'] : null;
        $description = (isset($data['description'])) ? $data['description'] : null;
        $expense = (isset($data['expense']) && is_bool($data['expense'])) ? $data['expense'] : null;
        $value = (isset($data['value']) && is_float($data['value'])) ? $data['value'] : null;
        $date = (isset($data['date'])) ? $data['date'] : null;
        $category = (isset($data['category'])) ? $data['category'] : null;
        $card = (isset($data['card'])) ? $data['card'] : null;
        $transfer_bank = (isset($data['transfer_bank'])) ? $data['transfer_bank'] : null;

        $this->setId($id);
        $this->setUser($user);
        $this->setDescription($description);
        $this->setExpense($expense);
        $this->setValue($value);
        $this->setDate($date);
        $this->setCategory($category);
        $this->setCard($card);
        $this->setTransferbank($transfer_bank);

        return $this;
    }

}
