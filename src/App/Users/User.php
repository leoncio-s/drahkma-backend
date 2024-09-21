<?php

namespace App\Users;

use App\Interfaces\Model;
use DateTime;
use Exception;
use App\Utils\PasswordUtils;
use App\Validators\StringValidator;

class User implements Model
{


    private ?int $id = null;
    private ?string $fullname = null;
    private ?string $email = null;
    private ?string $password = null;
    private ?string $phone_number = null;
    private ?bool $actived = false;
    private ?DateTime $email_verified_at = null;
    private ?DateTime $created_at = null;
    private ?DateTime $updated_at = null;


    public function __construct(int $id = null,
    string $fullname = null,
    string $email = null,
    string $password = null,
    string $phone_number = null,
    bool $actived = false,
    string $email_verified_at = null,
    string $created_at = null,
    string $updated_at = null) {
        $this->setId($id);
        $this->setFullName($fullname);
        $this->setEmail($email);
        $this->setPassword($password);
        $this->setPhoneNumber($phone_number);
        $this->setActived($actived);
        $this->setEmailVerifiedAt($email_verified_at);
        $this->setCreatedAt($created_at);
        $this->setUpdatedAt($updated_at);
    }
    


    public function getId(): ?int {
        return $this->id;
    }
    public function getFullName() : ?string {
        return $this->fullname;
    }
    public function getEmail() : ?string {
        return $this->email;
    }
    public function getPassword() : ?string{
        return $this->password;
    }
    public function getPhoneNumber() : ?string{
        return $this->phone_number;
    }
    public function getActived() : ?bool {
        return $this->actived;
    }
    public function getEmailVerifiedAt() : ?DateTime{
        return $this->email_verified_at;
    }
    public function getCreatedAt() : ?DateTime{
        return $this->created_at;
    }
    public function getUpdatedAt() : ?DateTime{
        return $this->updated_at;
    }

    public function setId(?int $id): void{
        $this->id = $id;
    }
    public function setFullName(?string $value): void {
        if($value != null)
            $this->fullname = (strlen($value) >= 3 && strlen($value) <= 100 && StringValidator::namesValidate($value)) ? $value : throw new Exception("invalid full name value for {$value}");
        
    }
    public function setEmail(?string $value): void {
        if($value != null)
            $this->email = (StringValidator::emailValidator($value)) ? filter_var($value, FILTER_SANITIZE_EMAIL) : throw new Exception("invalid e-mail value");
    }
    public function setPassword(?string $value): void{
        if($value)
            $this->password = (StringValidator::passwordValidator($value)) ? PasswordUtils::encoder($value) : throw new Exception("Invalid value for password field");
    }
    public function setPhoneNumber(?string $value): void{
        if($value != null)
            $this->phone_number = (StringValidator::phoneNumbersValidator($value)) ? $value : throw new Exception("invalid phone number");
    }
    public function setActived(?bool $value): void {
        $this->actived = $value;
    }
    public function setEmailVerifiedAt(?string $value): void{
        try{
            $this->email_verified_at = ($value != null)?new DateTime($value) : null;
        }catch(Exception){
            throw new Exception("invalid Email verified Date");
        }
    }
    public function setCreatedAt(?string $value): void{
        try{
            $this->created_at = ($value != null)?new DateTime($value) : null;
        }catch(Exception){
            throw new Exception("invalid created Date");
        }
    }
    public function setUpdatedAt(?string $value): void{
        try{
            $this->updated_at = ($value != null)?new DateTime($value) : null;
        }catch(Exception){
            throw new Exception("invalid updated Date");
        }
    }

    public function toJson() : string{
        return json_encode(
            [
                'id' => $this->getId(),
                'fullname' => $this->getFullName(),
                'email' => $this->getEmail(),
                // 'password' => $this->getPassword(),
                'phone_number' => $this->getPhoneNumber(),
                // 'actived' => $this->getActived(),
                // 'email_verified_at' => $this->getEmailVerifiedAt(),
                'created_at' => $this->getCreatedAt(),
                'updated_at' => $this->getUpdatedAt()
            ]
        );
    }
    public function toObject(array $data): Model{
        $id = (isset($data['id'])) ? $data['id'] : null;
        $fullname = (isset($data['fullname'])) ? $data['fullname'] : null;
        $email = (isset($data['email'])) ? $data['email'] : null;
        $password = (isset($data['password'])) ? $data['password'] : null;
        $phone_number = (isset($data['phone_number'])) ? $data['phone_number'] : null;
        $actived = (isset($data['actived'])) ? $data['actived'] : null;
        $email_verified_at = (isset($data['email_verified_at'])) ? $data['email_verified_at'] : null;
        $created_at = (isset($data['created_at'])) ? $data['created_at'] : null;
        $updated_at = (isset($data['updated_at'])) ? $data['updated_at'] : null;
        
        $this->setId($id);
        $this->setFullName($fullname);
        $this->setEmail($email);
        $this->password = $data['password'];
        $this->setPhoneNumber($phone_number);
        $this->setActived($actived);
        $this->setEmailVerifiedAt($email_verified_at);
        $this->setCreatedAt($created_at);
        $this->setUpdatedAt($updated_at);
        
        return $this;
    }

    public function toArray():array{
        return              [
            'id' => $this->getId(),
            'fullname' => $this->getFullName(),
            'email' => $this->getEmail(),
            // 'password' => $this->getPassword(),
            'phone_number' => $this->getPhoneNumber(),
            'actived' => $this->getActived(),
            'email_verified_at' => $this->getEmailVerifiedAt(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt()
        ];
    }

    public function toArraySave(){
        return              [
            'id' => $this->getId(),
            'fullname' => $this->getFullName(),
            'email' => $this->getEmail(),
            'password' => $this->getPassword(),
            'phone_number' => $this->getPhoneNumber(),
            'actived' => $this->getActived(),
            'email_verified_at' => $this->getEmailVerifiedAt(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt()
        ];
    }

    public static function validate(array $data): array{
        $errors = [
            "fullname" =>[],
            "email" =>[],
            "password" =>[],
            "conf_password" => [],
            "phone_number" =>[]
        ];
        $fullname = null;
        $email = null;
        $password = isset($data['password']) ? $data['password'] : null;;
        $conf_password = null;
        $phone_number = null;

        // Validação de nome
        if(!isset($data['fullname']) || $data['fullname'] == "") {
            array_push($errors["fullname"], "Campo obrigatório");
        }

        if(isset($data['fullname']) && (strlen($data['fullname']) < 3 || strlen($data['fullname']) >100)) {
            array_push($errors["fullname"], "O tamanho minímo para o campo é 3 e o máximo é 100");
        }

        if(isset($data['fullname']) && !StringValidator::namesValidate($data['fullname'])){
            array_push($errors["fullname"], "O campo possui caracteres ou valor inválido, verifique!");
        }else{
            $fullname = isset($data['fullname']) ? $data['fullname'] : null;
        }
        // fim validação do nome

        // Validação de email
        if(!isset($data['email']) || $data['email'] == "") {
            array_push($errors["email"], "Campo obrigatório");
        }

        if(isset($data['email']) && (strlen($data['email']) < 4 || strlen($data['email']) > 150)) {
            array_push($errors["email"], "O tamanho minímo para o campo é 4 e o máximo é 150");
        }

        if(isset($data['email']) && !StringValidator::emailValidator($data['email'])){
            array_push($errors["email"], "O campo possui caracteres ou valor inválido, verifique!");
        }else{
            $email = isset($data['email']) ? $data['email'] : null;
        }
        // fim validação do email

        
        // Validação de senha
        if(!isset($data['password']) || $data['password'] == "") {
            array_push($errors["password"], "Campo obrigatório");
        }

        if(isset($data['password']) && (strlen($data['password']) < 8 || strlen($data['password']) > 20)) {
            array_push($errors["password"], "O tamanho minímo para o campo é 8 e o máximo é 20");
        }

        if(isset($data['password']) && !StringValidator::passwordValidator($data['password'])){
            array_push($errors["password"], "O campo deve ter letras maiúsculas, minúscula, caracteres especias e número");
        }
        if((!isset($data['password']) || !isset($data["conf_password"])) || $data['password'] != $data['conf_password']){
            array_push($errors["password"], "Campos de senhas são diferentes");
        }
        // fim validação do senha        

        // Validação de telefone
        if(!isset($data['phone_number'])){
            array_push($errors["phone_number"], "O campo é obrigatório!");
        }else if(isset($data['phone_number']) && $data["phone_number"] != "" && (strlen($data['phone_number']) < 8 || strlen($data['phone_number']) > 14)) {
            array_push($errors["phone_number"], "O tamanho minímo para o campo é 8 e o máximo é 14");
        }else if(isset($data['phone_number']) && $data["phone_number"] != "" && !StringValidator::phoneNumbersValidator($data['phone_number'])){
            array_push($errors["phone_number"], "O campo possui caracteres ou valor inválido, verifique!");
        }else{
            $phone_number = isset($data['phone_number']) ? $data['phone_number'] : null;
        }
        // fim validação do telefone

        if(count($errors, COUNT_RECURSIVE) > 5){
            $data['conf_password'] = $data['password'];
            foreach($errors as $k => $v){
                if(count($errors[$k]) == 0) unset($errors[$k]);
            }
            return ['errors' => $errors, 'data'=>[
                'fullname' => $fullname,
                'email' => $email,
                'password' => $password,
                'conf_password' => isset($data['conf_password'])? $conf_password : '',
                'phone_number' => $phone_number
            ]];
        }else{
            $user = new User(fullname:$fullname, email:$email, password:$password, phone_number:$phone_number);
            return ['data'=>$user->toArraySave()];
        }

    }
}
