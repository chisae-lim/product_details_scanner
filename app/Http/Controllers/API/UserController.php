<?php

namespace App\Http\Controllers\API;

use Exception;
use Throwable;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Traits\UserQuery;
use App\Models\UserPermission;
use App\Http\Traits\FuncAssets;
use App\Http\Traits\ErrorAssets;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use FuncAssets, ErrorAssets, UserQuery;
    function getUsers(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 2], $this->readOnly);

        return User::where('id_user', '<>', $user->id_user)
            ->whereDoesntHave('permissions', function ($q) use ($user_id_perm) {
                $q->where('tbl_permission.id_permission', '<=', $user_id_perm);
            })
            ->with(['permissions', 'created_by', 'updated_by'])
            ->get()
            ->makeVisible(['acc_status', 'act_status', 'created_by', 'updated_by', 'created_at', 'updated_at']);
    }
    function createUser(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 2]);
        $request->validate([
            'name' => 'required|regex:/^[A-Za-z\s]+$/',
            'username' => 'required|regex:/^[a-zA-Z0-9]+$/',
            'password' => 'required|min:6|max:12|regex:/^[a-zA-Z0-9]+$/',
        ]);

        ###
        $name = $request->name;
        $id_permissions = $request->id_permissions;
        $usernameHash = $this->customHash($request->username, true);
        $passwordHash = $this->customHash($request->password, false);

        try {
            DB::beginTransaction();
            $acc = User::firstOrCreate(
                [
                    'username' => $usernameHash,
                ],
                [
                    'name' => $name,
                    'password' => $passwordHash,
                    'auth_token' => $this->customHash($usernameHash . $passwordHash, false),
                    'created_by' => $user->id_user,
                    'updated_by' => $user->id_user,
                ],
            );
            $valid_id_permissions = $this->validUserPermissions($user, $user_id_perm)
                ->pluck('id_permission')
                ->toArray();
            foreach ($id_permissions as $id_permission) {
                if (!in_array($id_permission, $valid_id_permissions)) {
                    DB::rollback();
                    return $this->requirementErrorMsg();
                }
                UserPermission::create([
                    'id_user' => $acc->id_user,
                    'id_permission' => $id_permission,
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->requirementErrorMsg();
        } catch (Throwable $th) {
            DB::rollback();
            return $this->requirementErrorMsg();
        }

        if (!$acc->wasRecentlyCreated) {
            throw ValidationException::withMessages([
                'username' => 'The username is already existed!.',
            ]);
        } else {
            $acc = User::where('id_user', $acc->id_user)
                ->with(['permissions', 'created_by', 'updated_by'])
                ->first();
        }
        return response(
            [
                'message' => 'The user has been created.',
                'user' => $acc->makeVisible(['acc_status', 'act_status', 'created_by', 'updated_by', 'created_at', 'updated_at']),
            ],
            200,
        );
    }
    function updateUser(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 2]);
        $request->validate([
            'name' => 'required|regex:/^[A-Za-z\s]+$/',
            'username' => 'nullable|regex:/^[a-zA-Z0-9]+$/',
            'password' => 'nullable|min:6|max:12|regex:/^[a-zA-Z0-9]+$/',
        ]);

        ###
        $id_user = $request->id_user;
        $name = $request->name;
        $id_permissions = $request->id_permissions;
        $username = $request->username;
        $password = $request->password;

        try {
            DB::beginTransaction();
            $acc = User::where('id_user', $id_user)
                ->with(['permissions', 'created_by', 'updated_by'])
                ->first();
            $acc->name = $name;
            if (!empty($username)) {
                $usernameHash = $this->customHash($username, true);
                $existed = User::where('username', '=', $usernameHash)
                    ->where('id_user', '<>', $acc->id_user)
                    ->first();
                if ($existed) {
                    throw ValidationException::withMessages([
                        'username' => 'The username is taken. Try another.',
                    ]);
                }
                $acc->username = $usernameHash;
            }
            if (!empty($password)) {
                $acc->password = $this->customHash($password, false);
            }
            if (!empty($username) || !empty($password)) {
                $acc->auth_token = md5($acc->username . $acc->password);
            }
            
            $valid_id_permissions = $this->validUserPermissions($user, $user_id_perm)
                ->pluck('id_permission')
                ->toArray();
            $old_id_permissions = $acc->permissions->pluck('id_permission')->toArray();
            foreach ($old_id_permissions as $id_permission) {
                if (!in_array($id_permission, $id_permissions)) {
                    if (!in_array($id_permission, $valid_id_permissions)) {
                        DB::rollback();
                        return $this->requirementErrorMsg();
                    }
                    UserPermission::where('id_user', $acc->id_user)
                        ->where('id_permission', $id_permission)
                        ->delete();
                }
            }
            foreach ($id_permissions as $id_permission) {
                if (!in_array($id_permission, $old_id_permissions)) {
                    if (!in_array($id_permission, $valid_id_permissions)) {
                        DB::rollback();
                        return $this->requirementErrorMsg();
                    }
                    UserPermission::create([
                        'id_user' => $acc->id_user,
                        'id_permission' => $id_permission,
                    ]);
                }
            }
            if ($acc->isDirty()) {
                $acc->updated_by = $user->id_user;
            }
            $acc->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->requirementErrorMsg();
        } catch (Throwable $th) {
            DB::rollback();
            return $this->requirementErrorMsg();
        }
        return response(
            [
                'message' => 'The user has been updated.',
                'user' => $acc->makeVisible(['acc_status', 'act_status', 'created_by', 'updated_by', 'created_at', 'updated_at']),
            ],
            200,
        );
    }
    function readUser(Request $request, $id_user)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 2], $this->readOnly);

        $acc = User::where('id_user', $id_user)
            ->whereDoesntHave('permissions', function ($q) use ($user_id_perm) {
                $q->where('tbl_permission.id_permission', '<=', $user_id_perm);
            })
            ->with(['permissions', 'created_by', 'updated_by'])
            ->first();
        if (!$acc) {
            return abort(417, 'User account not found.');
        }
        return response($acc->makeVisible(['acc_status', 'act_status', 'created_by', 'updated_by', 'created_at', 'updated_at']), 200);
    }
    function disableUser(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 2]);
        ###
        $id_user = $request->id_user;

        $acc = User::where('id_user', $id_user)
            ->with(['permissions', 'created_by', 'updated_by'])
            ->first();
        if (!$acc) {
            return abort(417, 'User account not found.');
        }
        $acc_id_perm = $this->permittedUser($acc, [1, 2], true, false);
        if ($acc_id_perm !== false && $acc_id_perm <= $user_id_perm) {
            return abort(417, 'User account not found.');
        }
        $acc->acc_status = 'DISABLED';
        if ($acc->isDirty()) {
            $acc->updated_by = $user->id_user;
        }
        $disabled = $acc->save();
        if (!$disabled) {
            return abort(422, 'Failed to disable user account.');
        }
        return response(
            [
                'message' => 'User account has been disabled.',
                'user' => $acc->makeVisible(['acc_status', 'act_status', 'created_by', 'updated_by', 'created_at', 'updated_at']),
            ],
            200,
        );
    }
    function enableUser(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 2]);
        ###
        $id_user = $request->id_user;

        $acc = User::where('id_user', $id_user)
            ->with(['permissions', 'created_by', 'updated_by'])
            ->first();
        if (!$acc) {
            return abort(417, 'User account not found.');
        }
        $acc_id_perm = $this->permittedUser($acc, [1, 2], true, false);
        if ($acc_id_perm !== false && $acc_id_perm <= $user_id_perm) {
            return abort(417, 'User account not found.');
        }
        $acc->acc_status = 'ENABLED';
        if ($acc->isDirty()) {
            $acc->updated_by = $user->id_user;
        }
        $enabled = $acc->save();
        if (!$enabled) {
            return abort(422, 'Failed to enable user account.');
        }
        return response(
            [
                'message' => 'User account has been enabled.',
                'user' => $acc->makeVisible(['acc_status', 'act_status', 'created_by', 'updated_by', 'created_at', 'updated_at']),
            ],
            200,
        );
    }
    function forbidUser(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 2]);
        ###
        $id_user = $request->id_user;

        $acc = User::where('id_user', $id_user)
            ->with(['permissions', 'created_by', 'updated_by'])
            ->first();
        if (!$acc) {
            return abort(417, 'User account not found.');
        }
        $acc_id_perm = $this->permittedUser($acc, [1, 2], true, false);
        if ($acc_id_perm !== false && $acc_id_perm <= $user_id_perm) {
            return abort(417, 'User account not found.');
        }
        $acc->act_status = 'DENIED';
        if ($acc->isDirty()) {
            $acc->updated_by = $user->id_user;
        }
        $forbade = $acc->save();
        if (!$forbade) {
            return abort(422, 'Failed to forbid user account.');
        }
        return response(
            [
                'message' => 'User account has been forbade.',
                'user' => $acc->makeVisible(['acc_status', 'act_status', 'created_by', 'updated_by', 'created_at', 'updated_at']),
            ],
            200,
        );
    }
    function permitUser(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 2]);
        ###
        $id_user = $request->id_user;

        $acc = User::where('id_user', $id_user)
            ->with(['permissions', 'created_by', 'updated_by'])
            ->first();
        if (!$acc) {
            return abort(417, 'User account not found.');
        }
        $acc_id_perm = $this->permittedUser($acc, [1, 2], true, false);
        if ($acc_id_perm !== false && $acc_id_perm <= $user_id_perm) {
            return abort(417, 'User account not found.');
        }
        $acc->act_status = 'ALLOWED';
        if ($acc->isDirty()) {
            $acc->updated_by = $user->id_user;
        }
        $permitted = $acc->save();
        if (!$permitted) {
            return abort(422, 'Failed to permit user account.');
        }
        return response(
            [
                'message' => 'User account has been permitted.',
                'user' => $acc->makeVisible(['acc_status', 'act_status', 'created_by', 'updated_by', 'created_at', 'updated_at']),
            ],
            200,
        );
    }
    function deleteUser(Request $request, $id_user)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 2]);

        $acc = User::where('id_user', $id_user)
            ->with(['permissions', 'created_by', 'updated_by'])
            ->first();
        if (!$acc) {
            return abort(417, 'User account not found.');
        }
        $acc_id_perm = $this->permittedUser($acc, [1, 2], true, false);
        if ($acc_id_perm !== false && $acc_id_perm <= $user_id_perm) {
            return abort(417, 'User account not found.');
        }
        try {
            DB::beginTransaction();
            $deleted = $acc->delete();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return abort(422, 'Failed to delete user account.');
        } catch (Throwable $th) {
            DB::rollback();
            return abort(422, 'Failed to delete user account.');
        }
        return response(
            [
                'message' => 'User account has been deleted.',
                'user' => $acc->makeVisible(['acc_status', 'act_status', 'created_by', 'updated_by', 'created_at', 'updated_at']),
            ],
            200,
        );
    }


    function getUsersWhoHasCoursePayments(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 12], $this->readOnly);

        return User::has('course_payments_where_created_by')->get();
    }
    function getUsersWhoHasTransportPayments(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 15], $this->readOnly);

        return User::has('transport_payments_where_created_by')->get();
    }
}
