<?php

namespace App\Http\Controllers;

use File;
use Image;
use Throwable;
use Carbon\Carbon;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    protected $readOnly = true;

    protected function permittedUser($user, $id_perms, $readOnly = false, $abort = true)
    {
        if ($readOnly || $user->act_status === 'ALLOWED') {
            $permissions = $user->permissions;
            foreach ($permissions as $permission) {
                if (in_array($permission->id_permission, $id_perms) && $permission->status === 'ENABLED') {
                    return $permission->id_permission;
                }
            }
        }
        return $abort ? abort(417, 'You are not permitted to perform the action.') : false;
    }

    function intMonthsBtwnTwoDates($from, $to, $format = 'd-m-Y')
    {
        try {
            $fromDate = Carbon::parse($from);
            $toDate = Carbon::parse($to);
            if ($fromDate->format($format) === $from && $toDate->format($format) === $to) {
                $diff_months = $fromDate->diffInMonths($toDate->addDay()); // important
                if ($fromDate->addMonthsNoOverflow($diff_months)->format($format) === $to) {
                    return $diff_months;
                }
                return false;
            }
            return false;
        } catch (Throwable $th) {
            return false;
        }
    }
    function intDaysBtwnTwoDates($from, $to, $format = 'd-m-Y')
    {
        try {
            $fromDate = Carbon::parse($from);
            $toDate = Carbon::parse($to);
            if ($fromDate->format($format) === $from && $toDate->format($format) === $to) {
                return $fromDate->diffInDays($toDate->addDay()); // important
            }
            return false;
        } catch (Throwable $th) {
            return false;
        }
    }

    function gtOneMonthBtwnTwoDates($from, $to, $format = 'd-m-Y')
    {
        try {
            $fromDate = Carbon::parse($from);
            $toDate = Carbon::parse($to);
            if ($fromDate->format($format) === $from && $toDate->format($format) === $to) {
                $diff_months = $fromDate->diffInMonths($toDate->addDay()); // important
                return $diff_months > 0;
            }
            return false;
        } catch (Throwable $th) {
            return false;
        }
    }

    //114px = 3cm, 152px = 4cm, 171px = 4.5cm, 189px = 5cm, 227px = 6cm

    function storeProfileImage($photoData, $folderName)
    {
        try {
            $photoName = 'IMG' . time() . '.png';
            $photoData = str_replace('data:image/png;base64,', '', $photoData);
            $photoData = str_replace(' ', '+', $photoData);
            $photoData = Image::make(base64_decode($photoData));
            $photo5x5 = clone $photoData;
            $photo3x4 = clone $photoData;
            $photo4x6 = clone $photoData;


            $left3x4 = floor((304 - 228) / 2);
            $left4x6 = floor((454 - 341) / 2);

            // 5x5
            $photo5x5->resize(378, 378)->save(public_path() . "/assets/images/$folderName/5x5/" . $photoName);
            // thumbnail
            $photo5x5->resize(150, 150)->save(public_path() . "/assets/images/$folderName/thumbnails/" . $photoName);
            $photo3x4
                ->resize(304, 304)
                ->crop(228, 304, $left3x4, 0)
                ->save(public_path() . "/assets/images/$folderName/3x4/" . $photoName);
            $photo4x6
                ->resize(454, 454)
                ->crop(341, 454, $left4x6, 0)
                ->save(public_path() . "/assets/images/$folderName/4x6/" . $photoName);
            return $photoName;
        } catch (Throwable $th) {
            return $th;
        }

    }
    function deleteProfileImage($photoName, $folderName)
    {
        try {
            $store_path_thumbnails = public_path() . "/assets/images/$folderName/thumbnails/";
            $store_path5x5 = public_path() . "/assets/images/$folderName/5x5/";
            $store_path3x4 = public_path() . "/assets/images/$folderName/3x4/";
            $store_path4x6 = public_path() . "/assets/images/$folderName/4x6/";
            if (File::exists($store_path_thumbnails . $photoName)) {
                File::delete($store_path_thumbnails . $photoName);
            }
            if (File::exists($store_path5x5 . $photoName)) {
                File::delete($store_path5x5 . $photoName);
            }
            if (File::exists($store_path3x4 . $photoName)) {
                File::delete($store_path3x4 . $photoName);
            }
            if (File::exists($store_path4x6 . $photoName)) {
                File::delete($store_path4x6 . $photoName);
            }
            return true;
        } catch (Throwable $th) {
            return $th;
        }

    }
}
