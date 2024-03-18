<?php
namespace App\Http\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

trait FuncAssets
{
    protected function customHash($string, $lower)
    {
        return md5(md5(md5(trim($lower ? strtolower($string) : $string))));
    }
    protected function encryptData($string)
    {
        return Crypt::encryptString($string);
    }
    protected function decryptData($string)
    {
        try {
            return Crypt::decryptString($string);
        } catch (DecryptException $e) {
            return false;
        }
    }
    // protected function decryptToken($encrypted_token)
    // {
    //     try {
    //         $decrypted_token = Crypt::decryptString($encrypted_token);
    //         return substr($decrypted_token, strpos($decrypted_token, '|') + 1);
    //     } catch (DecryptException $e) {
    //         return false;
    //     }
    // }
    // protected function checkAuthCookie($encrypted_token)
    // {
    //     $decrypted_token = $this->decryptToken($encrypted_token . '0');
    //     return $decrypted_token ? User::where('auth_token', $decrypted_token)->first() : false;
    // }
}
