<?php

namespace App\Http\Controllers\API;

use Throwable;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Traits\FuncAssets;
use App\Http\Traits\ErrorAssets;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class UnitController extends Controller
{
    use FuncAssets, ErrorAssets;
    function getUnits(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1], $this->readOnly);

        return Unit::all();
    }
    function createUnit(Request $request)
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
            $unit = Unit::firstOrCreate(
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
        if (!$unit->wasRecentlyCreated) {
            throw ValidationException::withMessages([
                'name' => 'The name is already existed!.',
            ]);
        } else {
            $unit = Unit::where('id_unit', $unit->id_unit)
                ->first();
        }
        return response(
            [
                'message' => 'The unit has been created.',
                'unit' => $unit->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function updateUnit(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);
        $request->validate([
            'name' => 'required',
            'label' => 'required',
        ]);

        ###
        $id_unit = $request->id_unit;
        $name = $request->name;
        $label = $request->label;

        $existed = Unit::where('name', $name)
            ->where('id_unit', '<>', $id_unit)
            ->first();
        if ($existed) {
            throw ValidationException::withMessages([
                'name' => 'The name is already existed!.',
            ]);
        }
        try {
            $unit = Unit::where('id_unit', $id_unit)
                ->first();
            $unit->name = $name;
            $unit->label = $label;
            $unit->save();
        } catch (Throwable $th) {
            return $this->requirementErrorMsg();
        }
        return response(
            [
                'message' => 'The unit has been updated.',
                'unit' => $unit->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function readUnit(Request $request, $id_unit)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1], $this->readOnly);

        $unit = Unit::where('id_unit', $id_unit)
            ->first();
        if (!$unit) {
            return abort(417, 'The unit not found.');
        }
        return response($unit->makeVisible(['created_by', 'updated_by']), 200);
    }
    function deleteUnit(Request $request, $id_unit)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);

        $unit = Unit::where('id_unit', $id_unit)
            ->first();
        if (!$unit) {
            return abort(417, 'The unit not found.');
        }
        try {
            $unit->delete();
        } catch (Throwable $th) {
            return abort(422, 'Failed to delete unit.');
        }
        return response(
            [
                'message' => 'The unit has been deleted.',
                'unit' => $unit->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
}
