<?php
namespace App\Http\Traits;

use App\Models\User;
use App\Models\Permission;

trait UserQuery
{
    protected function validUserPermissions($user, $user_id_perm)
    {
        return Permission::when($user_id_perm !== 1, function ($q) use ($user, $user_id_perm) {
            $q->where('id_permission', '>', $user_id_perm)
                ->whereHas('users', function ($u) use ($user) {
                    $u->where('tbl_user.id_user', $user->id_user);
                })
                ->where('status', 'ENABLED');
        })->when($user_id_perm === 1, function ($q) {
            $q->where('id_permission', '<>', '1');
        })->get();
    }
}
