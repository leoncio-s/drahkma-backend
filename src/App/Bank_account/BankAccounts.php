<?php

namespace App\Models;

use App\Interfaces\Model;
use App\Users\User;

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

    
    private int $id;
    private int $bankCode = null;
    private User $user = null;
    private string $bankName = null;
    private string $agency = null;
    private string $accountNumber = null;

    public function __construct(int $bankCode = null, User $userId = null, string $bankName = null, string $agency = null, string $accountNumber = null, int $id=null) {
        $this->setAccountNumber($accountNumber);
        $this->setBankCode($bankCode);
        $this->setAgency($agency);
        $this->setBankName($bankName);
        $this->setUser($userId);
        $this->setId($id);
    }


    public function getBankCode() : int{
        return $this->bankCode;
    }

    public function getUserId() : int{
        return $this->user->getId();
    }

    public function getBankName() : string{
        return $this->bankName;
    }

    public function getAgency() : string{
        return $this->agency;
    }

    public function getAccountNumber() : string{
        return $this->accountNumber;
    }

    public function getId() : int {
        return $this->id;
    }

    public function setId(int $id) : void {
        $this->id = ($id > 0)? $id : null;
    }

    public function setBankCode(int $bankCode) : void{
        
        $this->bankCode = ($bankCode > 0 ) ? $bankCode : null;
    }

    public function setUser(User $userId) : void{
        $this->user = ($userId instanceof User) ? $userId : null;
    }

    public function setBankName(string $bankName) : void{
        $this->bankName = (preg_match("^[A-Za-z0-9-]{3,100}$", $bankName))? $bankName : null;
    }

    public function setAgency(string $agency) : void{
        $this->agency = (preg_match("^[\d-]{3,8}$", $agency)) ? $agency : null;
    }

    public function setAccountNumber(string $accountNumber) : void{
        $this->accountNumber = (preg_match("^[\d-]{3,10}$", $accountNumber)) ? $accountNumber : null;
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

    public function toObject(array $data) : Model{
        $id = (isset($data['id']) && gettype($data['id']) == 'integer') ? $data['id'] : null;
        $bankCode = (isset($data['bankCode']) && gettype($data['bankCode']) == 'string') ? $data['bankCode'] : null;
        $userId = (isset($data['user']) && $data['user'] instanceof User) ? $data['user'] : null;
        $bankName = (isset($data['bankName']) && gettype($data['bankName']) == 'string') ? $data['bankName'] : null;
        $agency = (isset($data['agency']) && gettype($data['agency']) == 'string') ? $data['agency'] : null;
        $accountNumber = (isset($data['accountNumber']) && gettype($data['accountNumber']) == 'integer') ? $data['accountNumber'] : null;

        $this->setAccountNumber($accountNumber);
        $this->setBankCode($bankCode);
        $this->setAgency($agency);
        $this->setBankName($bankName);
        $this->setUser($userId);
        $this->setId($id);

        return $this;
    }
}
