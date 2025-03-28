<?php

namespace App\BankAccounts;

use App\Interfaces\Model;
use App\Validators\StringValidator;

use function PHPSTORM_META\type;

class BankAccounts implements Model
{

        /**
     * The attributes that are mass assignable.
     *
     * 
     */
    // protected $fillable = [
    // private int $bankCode;
    // private int $user_id;
    // private string $bank_name;
    // private string $agency;
    // private string $account_number;
    // ]; 

    
    private ?int $id = 0;
    private ?string $bankCode = null;
    private ?int $user = null;
    private ?string $bankName = null;
    private ?string $agency = null;
    private ?string $accountNumber = null;

    public function __construct(string $bankCode = null, int $userId = null, string $bankName = null, string $agency = null, string $accountNumber = null, int $id=null) {
        $this->setAccountNumber($accountNumber);
        $this->setBankCode($bankCode);
        $this->setAgency($agency);
        $this->setBankName($bankName);
        $this->setUser($userId);
        $this->setId($id);
    }


    public function getBankCode() : ?string{
        return $this->bankCode;
    }

    public function getUserId() : ?int{
        return $this->user;
    }

    public function getBankName() : ?string{
        return $this->bankName;
    }

    public function getAgency() : ?string{
        return $this->agency;
    }

    public function getAccountNumber() : ?string{
        return $this->accountNumber;
    }

    public function getId() : ?int {
        return $this->id;
    }

    public function setId(?int $id) : void {
        $this->id = ($id > 0)? $id : null;
    }

    public function setBankCode(?string $bankCode) : void{
        
        $this->bankCode = ($bankCode > 0 ) ? $bankCode : null;
    }

    public function setUser(?int $userId) : void{
        $this->user = ($userId != null) ? $userId : null;
    }

    public function setBankName(?string $bankName) : void{
        $this->bankName = ($bankName != null && StringValidator::descrValidate($bankName)) ? $bankName : null;
    }

    public function setAgency(?string $agency) : void{
        $this->agency = ($agency != null && StringValidator::onlyNumbers($agency)) ? $agency : null;
    }

    public function setAccountNumber(?string $accountNumber) : void{
        $this->accountNumber = ($accountNumber != null && StringValidator::onlyNumbers($accountNumber)) ? $accountNumber : null;
    }


    public function toJson() : string{
        return json_encode(
            [
                'id' => $this->getId(),
                'user' => $this->getUserId(),
                'bankCode' => $this->getBankCode(),
                'bankName' => $this->getBankName(),
                'agency' => $this->getAgency(),
                'accountNumber' => $this->getAccountNumber()
            ]
        );
    }

    public function toArray():array{
        return  [
            'id' => $this->getId(),
            'user' => $this->getUserId(),
            'bankCode' => $this->getBankCode(),
            'bankName' => $this->getBankName(),
            'agency' => $this->getAgency(),
            'accountNumber' => $this->getAccountNumber()
        ];
    }

    public function toObject(array $data) : Model{

        // var_dump($data);
        $id = (isset($data['id']) && gettype($data['id']) == 'integer') ? $data['id'] : null;
        $bankCode = (isset($data['bankCode']) && gettype($data['bankCode']) == 'string') ? $data['bankCode'] : null;
        $userId = (isset($data['user']) && gettype($data['user'])=="integer") ? $data['user'] : null;
        $bankName = (isset($data['bankName']) && gettype($data['bankName']) == 'string') ? $data['bankName'] : null;
        $agency = (isset($data['agency']) && gettype($data['agency']) == 'string') ? $data['agency'] : null;
        $accountNumber = (isset($data['accountNumber']) && gettype($data['accountNumber']) == 'string') ? $data['accountNumber'] : null;

        $this->setAccountNumber($accountNumber);
        $this->setBankCode($bankCode);
        $this->setAgency($agency);
        $this->setBankName($bankName);
        $this->setUser($userId);
        $this->setId($id);

        return $this;
    }

    public static function validate(array $data, bool $update = false) : array{
        $erros=[
            "id" => [],
            "bankCode"  => [],
            "bankName"  => [],
            "agency"    => [],
            "accountNumber" => []
        ];
        $id = (isset($data['id']) && gettype($data['id']) == 'integer') ? $data['id'] : null;
        $userId = (isset($data['user']) && gettype($data['user'])=="integer") ? $data['user'] : null;
        $bankCode = (isset($data['bankCode']) && gettype($data['bankCode']) == 'string') ? $data['bankCode'] : null;
        $bankName = (isset($data['bankName']) && gettype($data['bankName']) == 'string') ? $data['bankName'] : null;
        $agency = (isset($data['agency']) && gettype($data['agency']) == 'string') ? $data['agency'] : null;
        $accountNumber = (isset($data['accountNumber']) && gettype($data['accountNumber']) == 'string') ? $data['accountNumber'] : null;


        if($bankCode == null || $bankCode == ""){
            array_push($erros['bankCode'], "This field is required");
        }elseif(strlen($bankCode) <= 0 || strlen($bankCode) >= 10){
            array_push($erros['bankCode'], "This field size is between 1 and 10");
        }elseif(!StringValidator::onlyNumbers($bankCode)){
            array_push($erros['bankCode'], "This field accepts only numbers");
        }

        if($bankName == null || $bankName == ""){
            array_push($erros['bankName'], "This field is required");
        }elseif(strlen($bankName) < 3 || strlen($bankName) > 100){
            array_push($erros['bankName'], "This field size is between 3 and 10");
        }elseif(!StringValidator::descrValidate($bankName)){
            array_push($erros['bankName'], "This field is invalid");
        }

        if($agency == null || $agency == ""){
            array_push($erros['agency'], "This field is required");
        }elseif(strlen($agency) <= 0 || strlen($agency) > 8){
            array_push($erros['agency'], "This field size is between 1 and 8");
        }elseif(!StringValidator::onlyNumbers($agency)){
            array_push($erros['agency'], "This field is invalid");
        }

        if($accountNumber == null || $accountNumber == ""){
            array_push($erros['accountNumber'], "This field is required");
        }elseif(strlen($accountNumber) <= 2 || strlen($accountNumber) > 10){
            array_push($erros['accountNumber'], "This field size is between 2 and 10");
        }elseif(!StringValidator::onlyNumbers($agency)){
            array_push($erros['accountNumber'], "This field is invalid");
        }

        if($update){
            if(!isset($data['id'])){
                array_push($erros['id'], 'this field is required');
            }elseif(gettype($data['id']) == 'integer' && $data['id'] <= 0){
                array_push($erros['id'], 'invalid value for field');
            }
        }

        foreach($erros as $k=>$v){
            if(count($erros[$k]) == 0){
                unset($erros[$k]);
            }
        }

        if(count($erros) > 0){
            return[
                "errors" => $erros,
                "data" => $data
            ];
        }else{
            return ['data' => (new BankAccounts($bankCode, $userId, $bankName, $agency, $accountNumber, $id))->toArray()];
        }
    }
}
