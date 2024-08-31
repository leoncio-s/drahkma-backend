<?php

namespace App\Cards;

use App\Cards\Enums\CardTypeEnum;
use App\Cards\Enums\CardFlagsEnum;
use App\Interfaces\Model;
use App\Users\User;
use App\Validators\StringValidator;
use DateTime;
use Exception;

class Cards implements Model
{

    public ?int $id;
    public ?int $user;
    public ?CardTypeEnum $type;
    public ?string $brand;
    public ?CardFlagsEnum $flag;
    public ?string $expires_at;
    public ?string $last_4_digits;
    public ?int $invoice_day;

    public function __construct(int $id = null, CardTypeEnum $cardTypeEnum = null, string $brand=null, CardFlagsEnum $cardFlagsEnum = null, string $expires_at = null, string $last4Digits = null, int $invoide_day = null, int $user=null) {
        $this->setBrand($brand);
        $this->setExpiresAt($expires_at);
        $this->setFlag($cardFlagsEnum);
        $this->setId($id);
        $this->setLast4Digits($last4Digits);
        $this->setType($cardTypeEnum);
        $this->setUser($user);
        $this->setInvoiceDay($invoide_day);
    }




    public function getId() :  ?int{
        return $this->id;
    }
    public function getUserId():  ?int{
        return $this->user;
    }
    public function getType():  ?string{
        return $this->type->name;
    }
    public function getBrand() :  ?string{
        return $this->brand;
    }
    public function getFlag():  ?string{
        return $this->flag->name;
    }
    public function getExpiresAt():  ?string{
        return $this->expires_at;
    }
    public function getLast4Digits(): ?string{
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

    public function setId(?int $id) : void{
        $this->id= ($id >0)? $id : null;
    }
    public function setUser(?int $userId): void{
        $this->user =($userId != null && $userId >= 0) ? $userId : null;
    }
    public function setType(?CardTypeEnum $cardTypeEnum): void{
        $this->type = $cardTypeEnum;
    }
    public function setBrand(?string $brand) : void{
        $this->brand = ($brand != null && strlen($brand) >= 3 && strlen($brand) <= 50 && StringValidator::descrValidate($brand) )? $brand : "";
    }
    public function setFlag(?CardFlagsEnum $cardFlagsEnum): void{
        $this->flag = $cardFlagsEnum;
    }
    public function setExpiresAt(?string $expires): void{
        $this->expires_at = ($expires != null && StringValidator::cardsDigitsValidator($expires)) ? $expires : "";
    }
    public function setLast4Digits(?string $lastDigits):void{
        $this->last_4_digits = ($lastDigits!=null && StringValidator::cardsDigitsValidator($lastDigits)) ? $lastDigits : "";
    }

    public function setInvoiceDay(?int $invoiceDay): void {
        $this->invoice_day = ($invoiceDay != null && $invoiceDay > 0 && $invoiceDay < 31)? $invoiceDay : null;
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

    public function toArray(): array {
        return 
            [
                'id' => $this->getId(),
                'user' => $this->getUserId(),
                'type' => $this->getType(),
                'brand' => $this->getBrand(),
                'flag' => $this->getFlag(),
                'expires_at' => $this->getExpiresAt(),
                'last_4_digits' => $this->getLast4Digits(),
                'invoice_day' => $this->getInvoiceDay()
            ];
    }



    public function toObject(array $data) : Model {
        $id = (isset($data['id']) && is_int($data['id'])) ? $data['id'] : null;
        $user = (isset($data['user']) && gettype($data['user']) == "integer") ? $data['user'] : null;
        $cardTypeEnum = (isset($data['type'])) ? CardTypeEnum::tryFrom($data['type']) : null;
        $brand = (isset($data['brand'])) ? $data['brand'] : null;
        $cardFlagsEnum = (isset($data['flag'])) ? CardFlagsEnum::tryFrom($data['flag']) : null;
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

    public static function validate(array $data): array{
        $cardTypeEnum = (isset($data['type'])) ? CardTypeEnum::tryFrom($data['type']) : null;
        $brand = (isset($data['brand'])) ? $data['brand'] : null;
        $cardFlagsEnum = (isset($data['flag'])) ? CardFlagsEnum::tryFrom($data['flag']) : null;
        $expires_at = (isset($data['expires_at'])) ? date_parse_from_format('my', $data['expires_at']) : null;
        $last4Digits = (isset($data['last_4_digits'])) ? $data['last_4_digits'] : null;
        $invoice_day = (isset($data['invoice_day']) && is_int($data['invoice_day'])) ? $data['invoice_day'] : 1;

        $errors = [ 
            'type' => [],
            'brand' => [],
            'flag' => [],
            'expires_at' => [],
            'last_4_digits' => [],
            'invoice_day' => []
        ];

        if($cardTypeEnum == null || $cardTypeEnum == ""){
            array_push($errors['type'], "This field is required");
        }

        if($brand == null || $brand == ""){
            array_push($errors['brand'], "This field is required");
        }elseif(strlen($brand) <= 3 || strlen($brand) > 50){
            array_push($errors['brand'], "Min. size is 3 and max. is 50");
        }elseif(!StringValidator::descrValidate($brand)) array_push($errors['brand'], "Invalid value");

        if($cardFlagsEnum == null || $cardFlagsEnum == "") array_push($errors["flag"], "This field is required");

        if($expires_at == null || $expires_at == "") array_push($errors["expires_at"], "This field is required");
        elseif(!checkdate($expires_at['month'], 1, $expires_at['year'])) array_push($errors["expires_at"], "Invalid value from field");

        if(!($last4Digits == null || $last4Digits == "") && !StringValidator::cardsDigitsValidator($last4Digits)) array_push($errors['last_4_digits'], "Invalid value from field");

        if($cardTypeEnum==CardTypeEnum::Credit && $invoice_day == null || $invoice_day == "") array_push($errors["invoice_day"], "This field is required");
        elseif($invoice_day <= 0 || $invoice_day >30) array_push($errors['invoice_day'], "The value of the field must be between 1 and 30");

        foreach($errors as $k => $v){
            if(count($errors[$k])==0) unset($errors[$k]);
        }

        if(count($errors) > 0) return ["errors" => $errors, "data" => $data];
        else{

            $toRet = [
                    'type' => $cardTypeEnum->value,
                    'brand' => $brand,
                    'user' => $data['user'],
                    'flag' => $cardFlagsEnum->value,
                    'expires_at' => $data['expires_at'],
                    'last_4_digits' => $last4Digits,
                    'invoice_day' => $invoice_day
            ];
            return ['data' => $toRet];
        }
    }
}
