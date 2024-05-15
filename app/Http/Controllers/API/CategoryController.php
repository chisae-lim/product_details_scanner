<?php

namespace App\Http\Controllers\API;

use Throwable;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Traits\FuncAssets;
use App\Http\Traits\ErrorAssets;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    use FuncAssets, ErrorAssets;
    function getCategories(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 3], $this->readOnly);

        return Category::orderBy('name', 'asc')->get();
    }
    function createCategory(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);
        $request->validate([
            'name' => 'required',
        ]);

        ###
        $name = $request->name;

        try {
            $cetegory = Category::firstOrCreate(
                [
                    'name' => $name,
                ],
                [

                ],
            );
        } catch (Throwable $th) {
            return $this->requirementErrorMsg();
        }
        if (!$cetegory->wasRecentlyCreated) {
            throw ValidationException::withMessages([
                'name' => 'The name is already existed!.',
            ]);
        } else {
            $cetegory = Category::where('id_category', $cetegory->id_category)
                ->first();
        }
        return response(
            [
                'message' => 'The category has been created.',
                'category' => $cetegory->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function updateCategory(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);
        $request->validate([
            'name' => 'required',
        ]);

        ###
        $id_category = $request->id_category;
        $name = $request->name;

        $existed = Category::where('name', $name)
            ->where('id_category', '<>', $id_category)
            ->first();
        if ($existed) {
            throw ValidationException::withMessages([
                'name' => 'The name is already existed!.',
            ]);
        }
        try {
            $cetegory = Category::where('id_category', $id_category)
                ->first();
            $cetegory->name = $name;
            $cetegory->save();
        } catch (Throwable $th) {
            return $this->requirementErrorMsg();
        }
        return response(
            [
                'message' => 'The category has been updated.',
                'category' => $cetegory->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function readCategory(Request $request, $id_category)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1], $this->readOnly);

        $cetegory = Category::where('id_category', $id_category)
            ->first();
        if (!$cetegory) {
            return abort(417, 'The category not found.');
        }
        return response($cetegory->makeVisible(['created_by', 'updated_by']), 200);
    }
    function deleteCategory(Request $request, $id_category)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);

        $cetegory = Category::where('id_category', $id_category)
            ->first();
        if (!$cetegory) {
            return abort(417, 'The category not found.');
        }
        try {
            $cetegory->delete();
        } catch (Throwable $th) {
            return abort(422, 'Failed to delete category.');
        }
        return response(
            [
                'message' => 'The category has been deleted.',
                'category' => $cetegory->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
}
