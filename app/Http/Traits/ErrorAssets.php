<?php
namespace App\Http\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

trait ErrorAssets
{
    protected function credentialErrorMsg()
    {
        return abort(417, 'Action can not be done, You are not allowed!');
    }

    protected function requirementErrorMsg()
    {
        return abort(417, 'The provided information is not valid!.');
    }
}
