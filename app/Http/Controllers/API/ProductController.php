<?php

namespace App\Http\Controllers\API;

use Throwable;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Traits\FuncAssets;
use App\Http\Traits\ErrorAssets;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    use FuncAssets, ErrorAssets;
    function getProducts(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1], $this->readOnly);

        return Product::with(['created_by', 'updated_by'])
            ->get()
            ->makeVisible(['created_by', 'updated_by']);
    }
    function createProduct(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'length' => 'sometime|numeric',
            'width' => 'nullable|required_with:length|numeric',
            'height' => 'nullable|required_with:width|numeric',
        ]);

        ###
        $code = $request->code;
        $name = $request->name;
        $price = $request->price;
        $description = $request->description;
        $length = $request->length;
        $width = $request->width;
        $height = $request->height;
        $id_scale = $request->id_scale;
        $id_unit = $request->id_unit;
        $id_category = $request->id_category;
        $id_brand = $request->id_brand;

        try {
            $product = Product::firstOrCreate(
                [
                    'code' => $code,
                ],
                [
                    'name' => $name,
                    'price' => $price,
                    'description' => $description,
                    'length' => $length,
                    'width' => $width,
                    'height' => $height,
                    'id_scale' => $id_scale,
                    'id_unit' => $id_unit,
                    'id_category' => $id_category,
                    'id_brand' => $id_brand,
                ],
            );
        } catch (Throwable $th) {
            return $this->requirementErrorMsg();
        }
        if (!$product->wasRecentlyCreated) {
            throw ValidationException::withMessages([
                'code' => 'The code is already existed!.',
            ]);
        } else {
            $product = Product::where('id_product', $product->id_product)
                ->with(['created_by', 'updated_by'])
                ->first();
        }
        return response(
            [
                'message' => 'The product has been created.',
                'product' => $product->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function updateProduct(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'price' => 'required|numeric',
            'description' => 'required',
            'length' => 'sometime|numeric',
            'width' => 'nullable|required_with:length|numeric',
            'height' => 'nullable|required_with:width|numeric',
        ]);

        ###
        $id_product = $request->id_product;
        $code = $request->code;
        $name = $request->name;
        $price = $request->price;
        $description = $request->description;
        $length = $request->length;
        $width = $request->width;
        $height = $request->height;
        $id_scale = $request->id_scale;
        $id_unit = $request->id_unit;
        $id_category = $request->id_category;
        $id_brand = $request->id_brand;

        $existed = Product::where('code', $code)
            ->where('id_product', '<>', $id_product)
            ->first();
        if ($existed) {
            throw ValidationException::withMessages([
                'code' => 'The code is already existed!.',
            ]);
        }
        try {
            $product = Product::where('id_product', $id_product)
                ->with(['created_by', 'updated_by'])
                ->first();
            $product->code = $code;
            $product->name = $name;
            $product->price = $price;
            $product->description = $description;
            $product->length = $length;
            $product->width = $width;
            $product->height = $height;
            $product->id_scale = $id_scale;
            $product->id_unit = $id_unit;
            $product->id_category = $id_category;
            $product->id_brand = $id_brand;
            if ($product->isDirty()) {
                $product->updated_by = $user->id_user;
            }
            $product->save();
        } catch (Throwable $th) {
            return $this->requirementErrorMsg();
        }
        return response(
            [
                'message' => 'The product has been updated.',
                'product' => $product->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function readProduct(Request $request, $id_product)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1], $this->readOnly);

        $product = Product::where('id_product', $id_product)
            ->with(['created_by', 'updated_by'])
            ->first();
        if (!$product) {
            return abort(417, 'The product not found.');
        }
        return response($product->makeVisible(['created_by', 'updated_by']), 200);
    }
    function deleteProduct(Request $request, $id_product)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);

        $product = Product::where('id_product', $id_product)
            ->with(['created_by', 'updated_by'])
            ->first();
        if (!$product) {
            return abort(417, 'The product not found.');
        }
        try {
            $product->delete();
        } catch (Throwable $th) {
            return abort(422, 'Failed to delete product.');
        }
        return response(
            [
                'message' => 'The product has been deleted.',
                'product' => $product->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
}
