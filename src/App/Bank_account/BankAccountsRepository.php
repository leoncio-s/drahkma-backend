<?php

namespace App\Repository;

use App\Interfaces\RepositoryInterface;
use App\Models\BankAccounts;
use App\Models\Categories;
use App\Validators\BankAccountsValidator;
use Illuminate\Database\Eloquent\Model;

class BankAccountsRepository implements RepositoryInterface{
    public static function save(array $data): Model | null
    {

        $validator = BankAccountsValidator::make($data);
        if(isset($validator['user']) && !$validator->fails()){
            $validator = $validator->validate();
            $bank = new BankAccounts;
            $bank->bank_code        =   $validator['bank_code'];
            $bank->bank_name        =   $validator['bank_name'];
            $bank->agency           =   $validator['agency'];
            $bank->account_number   =   $validator['account_number'];
            $bank->user_id          =   $validator['user'];
            $bank->save();
            return $bank;
        }
        return null;
    }

    public static function update(array $data): ?Model
    {
        $validator = BankAccountsValidator::make($data);
        
        if(!$validator->fails()){
            $validator = $validator->validate();
            $bank = BankAccounts::where(['id'=> $validator['id'], 'user_id' => $validator['user']])->first();
            $bank->bank_code        =   $validator['bank_code'];
            $bank->bank_name        =   $validator['bank_name'];
            $bank->agency           =   $validator['agency'];
            $bank->account_number   =   $validator['account_number'];
            $bank->refresh();

            return $bank;
        }
        return $validator->failed();
    }

    public static function delete($data): bool
    {
        $bank = BankAccounts::where([
            'id' => $data['id'],
            'user_id' => $data['user']
        ])->first();

        return $bank->delete() >=1;
    }

    public static function get(int $id): ?Model
    {
        return Categories::where('id', $id)->first();
    }

    public static function getByIdAndUser(int $id, string $user){
        return Categories::where([
            'id' => $id,
            'user_id' => $user
        ])->get();
    }

    public static function getByUser(string $user){
        return Categories::where([
            'user_id' => $user
        ])->get();
    }
}