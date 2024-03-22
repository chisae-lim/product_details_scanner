<?php

namespace App\Http\Controllers\API;

use Image;
use Exception;
use Throwable;
use App\Models\Background;
use Illuminate\Http\Request;
use App\Http\Traits\FuncAssets;
use App\Http\Traits\ErrorAssets;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class BackgroundController extends Controller
{
    use FuncAssets, ErrorAssets;
    function getBackgrounds(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1], $this->readOnly);

        return Background::orderBy('status', 'desc')->get();
    }
    function createBackground(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);

        //1920x1080
        $request->validate([
            'image' => 'base64image|base64mimes:png,jpg,jpeg|base64dimensions:min_width=1920,min_height=1080,max_width=1920,max_height=1080',
        ]);
        ###
        $image = $request->image;
        try {
            DB::beginTransaction();
            $photoName = 'IMG' . time() . '.png';
            $image = str_replace(['data:image/png;base64,', 'data:image/jpg;base64,', 'data:image/jpeg;base64,'], '', $image);
            $image = str_replace(' ', '+', $image);
            $image = Image::make(base64_decode($image));
            $image->save(public_path() . "/assets/images/backgrounds/" . $photoName);
            $image->resize(384, 216)->save(public_path() . "/assets/images/backgrounds/thumbnails/" . $photoName);

            $background = Background::create([
                'name' => $photoName,
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return $this->requirementErrorMsg();
        } catch (Throwable $th) {
            DB::rollback();
            return $this->requirementErrorMsg();
        }
        $background = Background::where('id_background', $background->id_background)
            ->first();
        return response(
            [
                'message' => 'The background image has been added.',
                'background' => $background,
            ],
            200,
        );
    }
    function deleteBackground(Request $request, $id_background)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);

        $background = Background::where('id_background', $id_background)
            ->first();
        if (!$background) {
            return abort(417, 'The background not found.');
        }
        try {
            $this->deleteImage($background->name, 'backgrounds');
            $background->delete();
        } catch (Throwable $th) {
            return abort(422, 'Failed to delete background.');
        }
        return response(
            [
                'message' => 'The background image has been deleted.',
                'background' => $background,
            ],
            200,
        );
    }
    function activeBackground(Request $request, $id_background)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1]);

        $new_bg = Background::where('id_background', $id_background)
            ->first();
        $old_bg = Background::where('status', 'ENABLED')
            ->first();
        if (!$new_bg) {
            return abort(417, 'The background not found.');
        }
        try {
            DB::beginTransaction();
            if ($old_bg) {
                $old_bg->status = 'DISABLED';
                $old_bg->save();
            }
            $new_bg->status = 'ENABLED';
            $new_bg->save();
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
                'message' => 'The background image has been changed.',
                'new_background' => $new_bg,
                'old_background' => $old_bg,
            ],
            200,
        );
    }
    function getActiveBackground()
    {
        return Background::where('status', 'ENABLED')->first();
    }
}
