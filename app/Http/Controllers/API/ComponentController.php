<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Traits\UserQuery;
use App\Http\Controllers\Controller;

class ComponentController extends Controller
{
    use UserQuery;
    function getUserPermissions(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 2], $this->readOnly);

        return $this->validUserPermissions($user, $user_id_perm);
    }
}
