<?php

namespace App\Models;

use App\Enums\CardTypeEnum;
use App\Enums\CardFlagsEnum;
use App\Interfaces\Model;
use App\Users\User;
use App\Validator\StringValidator;
use Exception;

class Cards implements Model
{

    public int $id;
    public User $user;
    public CardTypeEnum $type;
    public string $brand;
    public CardFlagsEnum $flag;
    public string $expires_at;
    public string $last_4_digits;
    public int $invoice_day;

    public function __construct(int $id = null, CardTypeEnum $cardTypeEnum = null, string $brand=null, CardFlagsEnum $cardFlagsEnum = null, string $expires_at = null, string $last4Digits = null, int $invoide_day = null, User $user=null) {
        $this->setBrand($brand);
        $this->setExpiresAt($expires_at);
        $this->setFlag($cardFlagsEnum);
        $this->setId($id);
        $this->setLast4Digits($last4Digits);
        $this->setType($cardTypeEnum);
        $this->setUser($user);
        $this->setInvoiceDay($invoide_day);
    }




    public function getId() : int{
        return $this->id;
    }
    public function getUserId(): int{
        return $this->user->getId();
    }
    public function getType(): string{
        return $this->type->name;
    }
    public function getBrand() : string{
        return $this->brand;
    }
    public function getFlag(): string{
        return $this->flag->name;
    }
    public function getExpiresAt(): string{
        return $this->expires_at;
    }
    public function getLast4Digits():string{
        return $this->last_4_digits;
    }
    public function getInvoiceDay(){
        // date_default_timezone_set('America/Sao_Paulo');

        $day = $this->invoice_day;
        $month = date('m', time());
        $year = date('Y', time());
        $time = mktime(0,0,0, $month, $day, $year);

        $dayOfWeek = date('w', $time);

        if($dayOfWeek == 0){
        	$time = strtotime('+1 days ', $time);
        }else if($dayOfWeek == 6){
        	$time = strtotime('+2 days ', $time);
        }

        return date('Ymd', $time);
    }

    public function setId(int $id) : void{
        $this->id= ($id >0)? $id : throw new Exception("Invalid id value");
    }
    public function setUser(User $userId): void{
        $this->user =($userId instanceof User) ? $userId : throw new Exception("Invalid user value");
    }
    public function setType(CardTypeEnum $cardTypeEnum): void{
        $this->type = $cardTypeEnum;
    }
    public function setBrand(string $brand) : void{
        $this->brand = (StringValidator::descrValidate($brand) && strlen($brand) >= 3 && strlen($brand) <= 50)? $brand : throw new Exception("Invalid brand name");
    }
    public function setFlag(CardFlagsEnum $cardFlagsEnum): void{
        $this->flag = $cardFlagsEnum;
    }
    public function setExpiresAt(string $expires): void{
        $this->expires_at = (StringValidator::cardsDigitsValidator($expires)) ? $expires : throw new Exception("Invalid expires value");
    }
    public function setLast4Digits(string $lastDigits):void{
        $this->last_4_digits = (StringValidator::cardsDigitsValidator($lastDigits)) ? $lastDigits : throw new Exception("Invalid digits value");
    }

    public function setInvoiceDay(int $invoiceDay): void {
        $this->invoice_day = ($invoiceDay > 0 && $invoiceDay < 31)? $invoiceDay : throw new Exception("Invalid invoice day value");
    }

    public function toJson() : string {
        return json_encode(
            [
                'id' => $this->getId(),
                'user' => $this->getUserId(),
                'type' => $this->getType(),
                'brand' => $this->getBrand(),
                'flag' => $this->getFlag(),
                'expires_at' => $this->getExpiresAt(),
                'last_4_digits' => $this->getLast4Digits(),
                'invoice_day' => $this->getInvoiceDay()
            ]
        );
    }

    public function toObject(array $data) : Model {
        $id = (isset($data['id']) && is_int($data['id'])) ? $data['id'] : null;
        $user = (isset($data['user']) && $data['user'] instanceof User) ? $data['user'] : null;
        $cardTypeEnum = (isset($data['type'])) ? new CardTypeEnum($data['type']) : null;
        $brand = (isset($data['brand'])) ? $data['brand'] : null;
        $cardFlagsEnum = (isset($data['flag'])) ? new CardFlagsEnum($data['flag']) : null;
        $expires_at = (isset($data['expires_at'])) ? $data['expires_at'] : null;
        $last4Digits = (isset($data['last_4_digits'])) ? $data['last_4_digits'] : null;
        $invoice_day = (isset($data['invoice_day']) && is_int($data['invoice_day'])) ? $data['invoice_day'] : null;


        $this->setBrand($brand);
        $this->setExpiresAt($expires_at);
        $this->setFlag($cardFlagsEnum);
        $this->setId($id);
        $this->setLast4Digits($last4Digits);
        $this->setType($cardTypeEnum);
        $this->setUser($user);
        $this->setInvoiceDay($invoice_day);

        return $this;
    }
}
