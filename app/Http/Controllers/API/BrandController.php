<?php

namespace App\Http\Controllers\API;

use Throwable;
use App\Models\Brand;
use Illuminate\Http\Request;
use App\Http\Traits\FuncAssets;
use App\Http\Traits\ErrorAssets;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
    use FuncAssets, ErrorAssets;
    function getBrands(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1], $this->readOnly);

        return Brand::orderBy('name', 'asc')->get();
    }
    function createBrand(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);
        $request->validate([
            'name' => 'required',
        ]);

        ###
        $name = $request->name;

        try {
            $brand = Brand::firstOrCreate(
                [
                    'name' => $name,
                ],
                [

                ],
            );
        } catch (Throwable $th) {
            return $this->requirementErrorMsg();
        }
        if (!$brand->wasRecentlyCreated) {
            throw ValidationException::withMessages([
                'name' => 'The name is already existed!.',
            ]);
        } else {
            $brand = Brand::where('id_brand', $brand->id_brand)
                ->first();
        }
        return response(
            [
                'message' => 'The brand has been created.',
                'brand' => $brand->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function updateBrand(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);
        $request->validate([
            'name' => 'required',
        ]);

        ###
        $id_brand = $request->id_brand;
        $name = $request->name;

        $existed = Brand::where('name', $name)
            ->where('id_brand', '<>', $id_brand)
            ->first();
        if ($existed) {
            throw ValidationException::withMessages([
                'name' => 'The name is already existed!.',
            ]);
        }
        try {
            $brand = Brand::where('id_brand', $id_brand)
                ->first();
            $brand->name = $name;
            $brand->save();
        } catch (Throwable $th) {
            return $this->requirementErrorMsg();
        }
        return response(
            [
                'message' => 'The brand has been updated.',
                'brand' => $brand->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function readBrand(Request $request, $id_brand)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1], $this->readOnly);

        $brand = Brand::where('id_brand', $id_brand)
            ->first();
        if (!$brand) {
            return abort(417, 'The brand not found.');
        }
        return response($brand->makeVisible(['created_by', 'updated_by']), 200);
    }
    function deleteBrand(Request $request, $id_brand)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);

        $brand = Brand::where('id_brand', $id_brand)
            ->first();
        if (!$brand) {
            return abort(417, 'The brand not found.');
        }
        try {
            $brand->delete();
        } catch (Throwable $th) {
            return abort(422, 'Failed to delete brand.');
        }
        return response(
            [
                'message' => 'The brand has been deleted.',
                'brand' => $brand->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
}
