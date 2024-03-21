<?php

namespace App\Http\Controllers\API;

use Exception;
use Throwable;
use App\Models\Product;
use App\Models\Variant;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Http\Traits\FuncAssets;
use App\Http\Traits\ErrorAssets;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

class ProductController extends Controller
{
    use FuncAssets, ErrorAssets;
    function getProducts(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1], $this->readOnly);

        return Product::with(['images', 'colors', 'category', 'unit', 'brand', 'created_by', 'updated_by'])
            ->get()
            ->makeVisible(['created_by', 'updated_by']);
    }
    function createProduct(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);
        $customErrorMessage = [
            'p_code.required' => 'Product code is required.',
            'bar_code.required' => 'Barcode is required.',
            'bar_code.regex' => 'Barcode must contain only numeric characters.',
            'name_en.required' => 'English name is required.',
            'name_ch.required' => 'Chinese name is required.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a numeric value.',
        ];
        $request->validate([
            'p_code' => 'required',
            'bar_code' => 'required|regex:/^[0-9]+$/',
            'name_en' => 'required',
            'name_ch' => 'required',
            'price' => 'required|numeric',
        ], $customErrorMessage);
        ###
        $p_code = $request->p_code;
        $bar_code = $request->bar_code;
        $name_en = $request->name_en;
        $name_ch = $request->name_ch;
        $price = $request->price;
        $description = $request->description;
        $id_unit = $request->id_unit;
        $id_category = $request->id_category;
        $id_brand = $request->id_brand;
        $id_colors = $request->id_colors;
        $images = $request->images;

        $exists = Product::where('p_code', $p_code)
            ->orWhere('bar_code', $bar_code)
            ->get();

        $exceptions = [];
        foreach ($exists as $exist) {
            if (strcasecmp($exist->p_code, $p_code) === 0) {
                $exceptions['p_code'] = 'The Product Code is already existed.';
            }
            if (strcasecmp($exist->bar_code, $bar_code) === 0) {
                $exceptions['bar_code'] = 'The Bar Code is already existed.';
            }
        }
        if (!empty ($exceptions)) {
            throw ValidationException::withMessages($exceptions);
        }
        try {
            DB::beginTransaction();
            $image_names = [];
            if (!empty ($images)) {
                $index = 0;
                foreach ($images as $image) {
                    $image_names[] = $this->storeImage($image, 'products', $index);
                    $index++;
                }
            }
            $product = Product::create([
                'p_code' => $p_code,
                'bar_code' => $bar_code,
                'name_en' => $name_en,
                'name_ch' => $name_ch,
                'price' => $price,
                'description' => $description,
                'id_unit' => $id_unit,
                'id_brand' => $id_brand,
                'id_category' => $id_category,
                'created_by' => $user->id_user,
                'updated_by' => $user->id_user,
            ]);
            foreach ($id_colors as $id_color) {
                Variant::create([
                    'id_product' => $product->id_product,
                    'id_color' => $id_color,
                ]);
            }
            foreach ($image_names as $image_name) {
                ProductImage::create([
                    'id_product' => $product->id_product,
                    'name' => $image_name,
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
        $product = Product::where('id_product', $product->id_product)
            ->with(['images', 'colors', 'category', 'unit', 'brand', 'created_by', 'updated_by'])
            ->first();
        return response(
            [
                'message' => 'The product has been created.',
                'product' => $product->makeVisible(['created_by', 'updated_by']),
                'name' => $image_names
            ],
            200,
        );
    }
    function updateProduct(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);
        $customErrorMessage = [
            'p_code.required' => 'Product code is required.',
            'bar_code.required' => 'Barcode is required.',
            'bar_code.regex' => 'Barcode must contain only numeric characters.',
            'name_en.required' => 'English name is required.',
            'name_ch.required' => 'Chinese name is required.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a numeric value.',
        ];
        $request->validate([
            'p_code' => 'required',
            'bar_code' => 'required|regex:/^[0-9]+$/',
            'name_en' => 'required',
            'name_ch' => 'required',
            'price' => 'required|numeric',
        ], $customErrorMessage);

        ###
        $id_product = $request->id_product;
        $p_code = $request->p_code;
        $bar_code = $request->bar_code;
        $name_en = $request->name_en;
        $name_ch = $request->name_ch;
        $price = $request->price;
        $description = $request->description;
        $id_unit = $request->id_unit;
        $id_category = $request->id_category;
        $id_brand = $request->id_brand;
        $id_colors = $request->id_colors;
        $images = $request->images;
        $edited_images = $request->edited_images;
        $original_images = $request->original_images;

        $exists = Product::where(function ($q) use ($p_code, $bar_code) {
            $q->where('p_code', $p_code)
                ->orWhere('bar_code', $bar_code);
        })
            ->where('id_product', '<>', $id_product)
            ->get();

        $exceptions = [];
        foreach ($exists as $exist) {
            if (strcasecmp($exist->p_code, $p_code) === 0) {
                $exceptions['p_code'] = 'The Product Code is already existed.';
            }
            if (strcasecmp($exist->bar_code, $bar_code) === 0) {
                $exceptions['bar_code'] = 'The Bar Code is already existed.';
            }
        }
        if (!empty ($exceptions)) {
            throw ValidationException::withMessages($exceptions);
        }

        try {
            DB::beginTransaction();

            $product = Product::where('id_product', $id_product)
                ->first();
            $old_id_product_images = $product->images->pluck('id_product_image')->toArray();

            foreach ($original_images as $image) {
                if (!in_array($image['id_product_image'], $old_id_product_images)) {
                    return $this->requirementErrorMsg();
                }
            }

            // delete images
            $edited_id_product_images = collect($edited_images)->pluck('id_product_image')->toArray();

            foreach ($original_images as $image) {
                if (!in_array($image['id_product_image'], $edited_id_product_images)) {
                    $this->deleteImage($image['name'], 'products');
                    ProductImage::destroy($image['id_product_image']);
                }
            }

            // add images
            $image_names = [];
            if (!empty ($images)) {
                $index = 0;
                foreach ($images as $image) {
                    $image_names[] = $this->storeImage($image, 'products', $index);
                    $index++;
                }
            }
            foreach ($image_names as $image_name) {
                ProductImage::create([
                    'id_product' => $product->id_product,
                    'name' => $image_name,
                ]);
            }


            $product->p_code = $p_code;
            $product->bar_code = $bar_code;
            $product->name_en = $name_en;
            $product->name_ch = $name_ch;
            $product->price = $price;
            $product->description = $description;
            $product->id_unit = $id_unit;
            $product->id_brand = $id_brand;
            $product->id_category = $id_category;

            $old_id_colors = $product->colors->pluck('id_color')->toArray();
            foreach ($old_id_colors as $id_color) {
                if (!in_array($id_color, $id_colors)) {
                    Variant::where('id_product', $product->id_product)
                        ->where('id_color', $id_color)
                        ->delete();
                }
            }
            foreach ($id_colors as $id_color) {
                if (!in_array($id_color, $old_id_colors)) {
                    Variant::create([
                        'id_product' => $product->id_product,
                        'id_color' => $id_color,
                    ]);
                }
            }

            if ($product->isDirty()) {
                $product->updated_by = $user->id_user;
            }
            $product->save();
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->requirementErrorMsg();
        } catch (Throwable $th) {
            DB::rollback();
            return $this->requirementErrorMsg();
        }
        $product = Product::where('id_product', $product->id_product)
            ->with(['images', 'colors', 'category', 'unit', 'brand', 'created_by', 'updated_by'])
            ->first();
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
            ->with(['images', 'colors', 'category', 'unit', 'brand', 'created_by', 'updated_by'])
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
            DB::beginTransaction();
            $images = $product->images;
            foreach ($images as $image) {
                $this->deleteImage($image->name, 'products');
            }
            $product->delete();
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
                'message' => 'The product has been deleted.',
                'product' => $product->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }

    function searchProductByCode(Request $request, $code)
    {
        return Product::where('p_code', $code)->orWhere('bar_code', $code)->first();
    }

    function getProductByBarCode(Request $request, $bar_code)
    {
        return Product::where('bar_code', $bar_code)
            ->with(['images', 'colors', 'category', 'unit', 'brand'])
            ->first();
    }

    function getProductByProductCode(Request $request, $p_code)
    {
        return Product::where('p_code', $p_code)
            ->with(['images', 'colors', 'category', 'unit', 'brand'])
            ->first();
    }
}
