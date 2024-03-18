<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Traits\FuncAssets;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use FuncAssets;
    function login(Request $request, Response $response)
    {
        $auth_token = $request->cookie('AUTH-TOKEN');
        $user = User::where('auth_token', $auth_token)
            ->with('permissions')
            ->first();
        if ($user) {
            return response([
                'message' => 'You have already logged in.',
                'user' => $user->makeVisible(['act_status', 'acc_status']),
            ]);
        }

        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);
        $usernameHash = $this->customHash($request->username, true);
        $passwordHash = $this->customHash($request->password, false);

        $user = User::where('username', '=', $usernameHash)
            ->with('permissions')
            ->first();
        if (!$user) {
            throw ValidationException::withMessages([
                'username' => 'The username does not exist.',
            ]);
        }
        if ($user->password !== $passwordHash) {
            throw ValidationException::withMessages([
                'password' => 'The password is incorrect.',
            ]);
        }
        if ($user->acc_status === 'DISABLED') {
            throw ValidationException::withMessages([
                'username' => 'Your account has been disabled.',
            ]);
        }

        if ($request->remember_me) {
            $token = md5($usernameHash . $passwordHash . time());
            $expiry = 60 * 60 * 24 * 7;
            if ($user->rem_token != null && $user->rem_expiry != null && $user->rem_expiry - time() > 0) {
                $expiry = $user->rem_expiry - time();
            } else {
                $user->rem_token = $token;
                $user->rem_expiry = $expiry + time();
                $updateToken = $user->update();
            }
            $response->cookie('REM-TOKEN', $user->rem_token, $expiry / 60, '/');
        }
        $response->setContent([
            'message' => 'You have successfully logged in.',
            'user' => $user->makeVisible(['act_status', 'acc_status']),
        ]);
        return $response->cookie('AUTH-TOKEN', $user->auth_token, 0, '/');
    }
    function logout()
    {
        return response(['message' => 'You have successfully logged out.'])
            ->cookie('REM-TOKEN', 'deleted', -1, '/')
            ->cookie('AUTH-TOKEN', 'deleted', -1, '/');
    }
    function verify(Request $request)
    {
        $auth_token = $request->cookie('AUTH-TOKEN');
        $user = User::where('auth_token', $auth_token)
            ->where('acc_status', 'ENABLED')
            ->with('permissions')
            ->first();
        if ($user) {
            return response(
                [
                    'message' => 'You are logged in.',
                    'user' => $user->makeVisible(['act_status', 'acc_status']),
                ],
                200,
            );
        }
        $rem_token = $request->cookie('REM-TOKEN');
        $user = User::where('rem_token', $rem_token)
            ->where('acc_status', 'ENABLED')
            ->with('permissions')
            ->first();
        if ($user && $user->rem_token != null && $user->rem_expiry != null && $user->rem_expiry - time() > 0) {
            return response(
                [
                    'message' => 'You are logged in.',
                    'user' => $user->makeVisible(['act_status', 'acc_status']),
                ],
                200,
            )->cookie('AUTH-TOKEN', $user->auth_token, 0, '/');
        }
        return abort(401, 'The user is unauthorized.');
    }
    function changePassword(Request $request)
    {
        $request->validate(
            [
                'current_pass' => 'required',
                'new_pass' => 'required|min:8|max:12',
                'confirm_pass' => 'required|same:new_pass',
            ],
            [
                'confirm_pass.same' => 'The password confirmation does not match.',
            ],
        );

        $user = $request->user;
        $current_pass = $request->current_pass;
        $new_pass = $request->new_pass;
        $confirm_pass = $request->confirm_pass;

        $currentPassHash = $this->customHash($current_pass, false);
        if ($user->password !== $currentPassHash) {
            throw ValidationException::withMessages([
                'current_pass' => 'The current password is incorrect!.',
            ]);
        }
        if ($current_pass === $new_pass) {
            throw ValidationException::withMessages([
                'new_pass' => 'The new password can not be the same as current password!.',
            ]);
        }

        $newPassHash = $this->customHash($new_pass, false);
        $user->password = $newPassHash;
        $user->auth_token = $this->customHash($user->username . $newPassHash, false);
        $user->rem_token = null;
        $user->rem_expiry = null;
        if (!$user->save()) {
            return abort(422, 'Failed to change password.');
        }
        return response(
            [
                'message' => 'Your password has been changed.',
                'user' => $user->makeVisible(['act_status', 'acc_status']),
            ],
            200,
        );
    }
}
