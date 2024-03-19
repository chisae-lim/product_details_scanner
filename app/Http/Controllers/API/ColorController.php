<?php

namespace App\Http\Controllers\API;

use Throwable;
use App\Models\Color;
use Illuminate\Http\Request;
use App\Http\Traits\FuncAssets;
use App\Http\Traits\ErrorAssets;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class ColorController extends Controller
{
    use FuncAssets, ErrorAssets;
    function getColors(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1], $this->readOnly);

        return Color::all();
    }
    function createColor(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);
        $request->validate([
            'name' => 'required',
            'code' => 'required',
        ]);

        ###
        $name = $request->name;
        $code = strtoupper($request->code);

        try {
            $color = Color::firstOrCreate(
                [
                    'name' => $name,
                ],
                [
                    'code' => $code,
                ],
            );
        } catch (Throwable $th) {
            return $this->requirementErrorMsg();
        }
        if (!$color->wasRecentlyCreated) {
            throw ValidationException::withMessages([
                'name' => 'The name is already existed!.',
            ]);
        } else {
            $color = Color::where('id_color', $color->id_color)
                ->first();
        }
        return response(
            [
                'message' => 'The color has been created.',
                'color' => $color->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function updateColor(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);
        $request->validate([
            'name' => 'required',
            'code' => 'required',
        ]);

        ###
        $id_color = $request->id_color;
        $name = $request->name;
        $code = strtoupper($request->code);

        $existed = Color::where('name', $name)
            ->where('id_color', '<>', $id_color)
            ->first();
        if ($existed) {
            throw ValidationException::withMessages([
                'name' => 'The color name is already existed!.',
            ]);
        }
        try {
            $color = Color::where('id_color', $id_color)
                ->first();
            $color->name = $name;
            $color->code = $code;
            $color->save();
        } catch (Throwable $th) {
            return $this->requirementErrorMsg();
        }
        return response(
            [
                'message' => 'The color has been updated.',
                'color' => $color->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function readColor(Request $request, $id_color)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1], $this->readOnly);

        $color = Color::where('id_color', $id_color)
            ->first();
        if (!$color) {
            return abort(417, 'The color not found.');
        }
        return response($color->makeVisible(['created_by', 'updated_by']), 200);
    }
    function deleteColor(Request $request, $id_color)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);

        $color = Color::where('id_color', $id_color)
            ->first();
        if (!$color) {
            return abort(417, 'The color not found.');
        }
        try {
            $color->delete();
        } catch (Throwable $th) {
            return abort(422, 'Failed to delete color.');
        }
        return response(
            [
                'message' => 'The color has been deleted.',
                'color' => $color->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
}
