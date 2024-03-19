<?php

namespace App\Http\Controllers\API;

use Throwable;
use App\Models\Scale;
use Illuminate\Http\Request;
use App\Http\Traits\FuncAssets;
use App\Http\Traits\ErrorAssets;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class ScaleController extends Controller
{
    use FuncAssets, ErrorAssets;
    function getScales(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1], $this->readOnly);

        return Scale::all();
    }
    function createScale(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);
        $request->validate([
            'name' => 'required',
            'label' => 'required',
        ]);

        ###
        $name = $request->name;
        $label = $request->label;

        try {
            $scale = Scale::firstOrCreate(
                [
                    'name' => $name,
                ],
                [
                    'label' => $label,
                ],
            );
        } catch (Throwable $th) {
            return $this->requirementErrorMsg();
        }
        if (!$scale->wasRecentlyCreated) {
            throw ValidationException::withMessages([
                'name' => 'The name is already existed!.',
            ]);
        } else {
            $scale = Scale::where('id_scale', $scale->id_scale)
                ->first();
        }
        return response(
            [
                'message' => 'The scale has been created.',
                'scale' => $scale->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function updateScale(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);
        $request->validate([
            'name' => 'required',
            'label' => 'required',
        ]);

        ###
        $id_scale = $request->id_scale;
        $name = $request->name;
        $label = $request->label;

        $existed = Scale::where('name', $name)
            ->where('id_scale', '<>', $id_scale)
            ->first();
        if ($existed) {
            throw ValidationException::withMessages([
                'name' => 'The name is already existed!.',
            ]);
        }
        try {
            $scale = Scale::where('id_scale', $id_scale)
                ->first();
            $scale->name = $name;
            $scale->label = $label;
            $scale->save();
        } catch (Throwable $th) {
            return $this->requirementErrorMsg();
        }
        return response(
            [
                'message' => 'The scale has been updated.',
                'scale' => $scale->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function readScale(Request $request, $id_scale)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1], $this->readOnly);

        $scale = Scale::where('id_scale', $id_scale)
            ->first();
        if (!$scale) {
            return abort(417, 'The scale not found.');
        }
        return response($scale->makeVisible(['created_by', 'updated_by']), 200);
    }
    function deleteScale(Request $request, $id_scale)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);

        $scale = Scale::where('id_scale', $id_scale)
            ->first();
        if (!$scale) {
            return abort(417, 'The scale not found.');
        }
        try {
            $scale->delete();
        } catch (Throwable $th) {
            return abort(422, 'Failed to delete scale.');
        }
        return response(
            [
                'message' => 'The scale has been deleted.',
                'scale' => $scale->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
}
