<?php

namespace App\Items;

use App\Cards\Cards;
use App\Categories\Categories;
use App\Interfaces\Model;
use App\TransfersBank\TransferBank;
use App\Users\User;
use App\Validators\StringValidator;
use DateTime;
use Exception;

class Items implements Model
{
    private ?int $id;
    private ?int $user;
    private ?string $description;
    private ?bool $expense;
    private ?float $value;
    private ?DateTime $date;
    private ?Categories $category;
    private ?Cards $card;
    private ?TransferBank $transfer_bank;


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



    public function getId(): ?int{
        return $this->id;
    }
    public function getUserId():?int{
        return $this->user;
    }
    public function getDescription():?string{
        return $this->description;
    }
    public function getExpense(): ?bool{
        return $this->expense;
    }
    public function getValue():?float{
        return $this->value;
    }
    public function getDate():?DateTime{
        return $this->date;
    }
    public function getCategory():?Categories{
        return $this->category;
    } 
    public function getCategoryDescription():?string{
        return ($this->category != null) ? $this->category->getDescription() : null;
    }
    public function getCard():?Cards{
        return $this->card;
    }
    public function getCardsBrand():?string{
        return $this->card->getBrand();
    }
    public function getTransferbank():?TransferBank{
        return $this->transfer_bank;
    }

    public function setId(?int $value): void{
        $this->id = ($value > 0) ? $value : null;
    }
    public function setUser(?int $value):void{
        $this->user = ($value >0 ) ? $value : null;
    }
    public function setDescription(?string $value):void{
        $this->description = ($value != null && StringValidator::descrValidate($value) && strlen($value) > 3 && strlen($value) <= 100) ? $value :  null;
    }
    public function setExpense(?bool $value): void{
        $this->expense = $value;
    }
    public function setValue(?float $value):void{
        $this->value = $value;
    }
    public function setDate(?string $value):void{
        try{
            $this->date = new DateTime($value);
        }catch(Exception){
            $this->date = null;
        }
    }
    public function setCategory(?Categories $value):void{
        $this->category = ($value != null) ? $value : null;
    }
    public function setCard(?Cards $value):void{
        $this->card = ($value != null) ? $value : null;
    }
    public function setTransferbank(?TransferBank $value):void{
        $this->transfer_bank = ($value != null) ? $value : null;
    }


    public function toJson(): string
    {
        return json_encode(
            [
                'id' => $this->getId(),
                'description' => $this->getDescription(),
                'expense' => $this->getExpense(),
                'value' => $this->getValue(),
                'date' => $this->getDate(),
                'category' => $this->getCategoryDescription(),
                'card' => $this->getCard()->toJson(),
                'transfer_bank' => $this->getTransferbank()->toJson()
            ]
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'description' => $this->getDescription(),
            'expense' => $this->getExpense(),
            'value' => $this->getValue(),
            'date' => $this->getDate(),
            'category' => ($this->getCategory() != null) ? $this->getCategory()->toArray() : $this->getCategory(),
            'card' => ($this->getCard() != null) ? $this->getCard()->toArray() : $this->getCard(),
            'transfer_bank' => ($this->getTransferbank() != null) ? $this->getTransferbank()->toArray() : $this->getTransferbank()
        ];
    }

    public function toObject(array $data): Model
    {
        $id = (isset($data['id']) && is_int($data['id'])) ? $data['id'] : null;
        $user = (isset($data['user']) && is_int($data['user'])) ? $data['user'] : null;
        $description = (isset($data['description'])) ? $data['description'] : null;
        $expense = (isset($data['expense'])) ? boolval($data['expense']) : null;
        $value = (isset($data['value']) && is_float($data['value'])) ? $data['value'] : null;
        $date = (isset($data['date'])) ? $data['date'] : null;
        $category = (isset($data['category']) || $data['category'] != null) ? (new Categories())->toObject(json_decode($data['category'], true)) : null;
        $card = (isset($data['card']) || $data['card'] != null) ? (new Cards())->toObject(json_decode($data['card'], true)) : null;
        $transfer_bank = (isset($data['transfer_bank']) || $data['transfer_bank'] != null) ? (new TransferBank())->toObject(json_decode($data['transfer_bank'], true)) : null;

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

    
    public static function validate(array $data): array
    {
        // $id = (isset($data['id']) && is_int($data['id'])) ? $data['id'] : null;
        $user = (isset($data['user']) && is_int($data['user'])) ? $data['user'] : null;
        $description = (isset($data['description'])) ? $data['description'] : null;
        $expense = (isset($data['expense']) && is_bool($data['expense'])) ? $data['expense'] : true;
        $value = (isset($data['value']) && is_float($data['value'])) ? $data['value'] : null;
        $date = (isset($data['date'])) ? DateTime::createFromFormat('Ymd', $data['value']) : null;
        $category = (isset($data['category']) && is_int($data['category'])) ? $data['category'] : null;
        $card = (isset($data['card']) && is_int($data['card'])) ? $data['card'] : null;
        $transfer_bank = (isset($data['transfer_bank'])) ? TransferBank::validate($data['transfer_bank']) : null;

        $errors =   [
            'description'   => [],
            'expense'       => [],
            'value'         => [],
            'category'      => [],
            'date'          => [],
            'card'          => [],
            'transfer_bank' => []
        ];

        if($description == null || $description == "") array_push($errors['description'], "Field is required");
        elseif(!(strlen($description) >= 5 && strlen($description) <= 100)) array_push($errors['description'], "Min. Length is 5 and max. is 100d");
        elseif(!StringValidator::descrValidate($description)) array_push($errors['description'], "Invalid value for field");

        if($date == null) array_push($errors['date'], "Field is required");
        elseif(!$date) array_push($errors['date'], "Invalid value from field");

        if($value == null ) array_push($errors['value'], "Field is required");
        elseif($value < 0) array_push($errors['value'], "The value cannot be less than zero");

        if($transfer_bank == null && $card == null && $expense){
            array_push($errors["card"], "This field is required if transfer_bank is null and is an expense");
            array_push($errors['transfer_bank'], "This field is required if card is null and is an expense");
        }

        if($expense == false && $card != null) array_push($errors['card'], "A recipe cannot be recorded as a card");

        if($transfer_bank != null){
            $transfer_bank_validate = TransferBank::validate($data['transfer_bank']);
            if(isset($transfer_bank_validate['errors'])) array_push($errors['transfer_bank'], $transfer_bank_validate['errors']);
        }

        if($category == null) array_push($errors['category'], "This field is required");

        foreach($errors as $k => $v){
            if(count($errors[$k], COUNT_RECURSIVE) == 0) unset($errors[$k]);
        }

        if(count($errors, COUNT_RECURSIVE) > 7) {
            return [
                "errors" => $errors,
                "data" => $data,
            ];
        }

        $data2 =   [
            'user'          => $user,
            'description'   => $description,
            'expense'       => $expense,
            'value'         => $value,
            'category'      => $category,
            'date'          => ($date != null) ? $date->format('Ymd') : null,
            'card'          => $card,
            'transfer_bank' => $transfer_bank['data']
        ];
        return ["data" => $data2];
    }

}
