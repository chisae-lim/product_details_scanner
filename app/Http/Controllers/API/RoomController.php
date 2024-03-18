<?php

namespace App\Http\Controllers\API;

use Throwable;
use Carbon\Carbon;
use App\Models\Room;
use App\Models\Session;
use Illuminate\Http\Request;
use App\Http\Traits\RoomQuery;
use App\Http\Traits\FuncAssets;
use App\Http\Traits\ErrorAssets;
use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;

class RoomController extends Controller
{
    use FuncAssets, ErrorAssets, RoomQuery;
    function getRooms(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 5], $this->readOnly);

        return Room::with('created_by', 'updated_by')
            ->get()
            ->makeVisible(['created_by', 'updated_by']);
    }
    function createRoom(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 5]);
        $request->validate([
            'name' => 'required|regex:/^[A-Za-z0-9\s]+$/',
            'capacity' => 'required|integer|gt:0',
        ]);

        ###
        $name = $request->name;
        $capacity = $request->capacity;

        try {
            $room = Room::firstOrCreate(
                [
                    'name' => $name,
                ],
                [
                    'capacity' => $capacity,
                    'created_by' => $user->id_user,
                    'updated_by' => $user->id_user,
                ],
            );
        } catch (Throwable $th) {
            return $this->requirementErrorMsg();
        }
        if (!$room->wasRecentlyCreated) {
            throw ValidationException::withMessages([
                'name' => 'The name is already existed!.',
            ]);
        } else {
            $room = Room::where('id_room', $room->id_room)
                ->with('created_by', 'updated_by')
                ->first();
        }
        return response(
            [
                'message' => 'The room has been created.',
                'room' => $room->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function updateRoom(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 5]);
        $request->validate([
            'name' => 'required|regex:/^[A-Za-z0-9\s]+$/',
            'capacity' => 'required|integer|gt:0',
        ]);

        ###
        $id_room = $request->id_room;
        $name = $request->name;
        $capacity = $request->capacity;

        $existed = Room::where('name', $name)
            ->where('id_room', '<>', $id_room)
            ->first();
        if ($existed) {
            throw ValidationException::withMessages([
                'name' => 'The room name is already existed!.',
            ]);
        }
        try {
            $room = Room::where('id_room', $id_room)
                ->with(['created_by', 'updated_by'])
                ->first();
            $room->name = $name;
            $room->capacity = $capacity;
            if ($room->isDirty()) {
                $room->updated_by = $user->id_user;
            }
            $room->save();
        } catch (Throwable $th) {
            return $this->requirementErrorMsg();
        }
        return response(
            [
                'message' => 'The room has been updated.',
                'room' => $room->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function readRoom(Request $request, $id_room)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 5], $this->readOnly);

        $room = Room::where('id_room', $id_room)
            ->with(['created_by', 'updated_by'])
            ->first();
        if (!$room) {
            return abort(417, 'Room not found.');
        }
        return response($room->makeVisible(['created_by', 'updated_by']), 200);
    }
    function disableRoom(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 5]);
        ###
        $id_room = $request->id_room;

        $room = Room::where('id_room', $id_room)
            ->with(['created_by', 'updated_by'])
            ->first();
        if (!$room) {
            return abort(417, 'The room not found.');
        }
        $room->status = 'DISABLED';
        if ($room->isDirty()) {
            $room->updated_by = $user->id_user;
        }
        $disabled = $room->save();
        if (!$disabled) {
            return abort(422, 'Failed to disable room.');
        }
        return response(
            [
                'message' => 'The room has been disabled.',
                'room' => $room->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function enableRoom(Request $request)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 5]);
        ###
        $id_room = $request->id_room;

        $room = Room::where('id_room', $id_room)
            ->with(['created_by', 'updated_by'])
            ->first();
        if (!$room) {
            return abort(417, 'The room not found.');
        }
        $room->status = 'ENABLED';
        if ($room->isDirty()) {
            $room->updated_by = $user->id_user;
        }
        $enabled = $room->save();
        if (!$enabled) {
            return abort(422, 'Failed to enable room.');
        }
        return response(
            [
                'message' => 'The room has been enabled.',
                'room' => $room->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }
    function deleteRoom(Request $request, $id_room)
    {
        $user = $request->user;
        $user_id_perm = $this->permittedUser($user, [1, 5]);

        $room = Room::where('id_room', $id_room)
            ->with(['created_by', 'updated_by'])
            ->first();
        if (!$room) {
            return abort(417, 'The room not found.');
        }
        try {
            $room->delete();
        } catch (Throwable $th) {
            return abort(422, 'Failed to delete room.');
        }
        return response(
            [
                'message' => 'The room has been deleted.',
                'room' => $room->makeVisible(['created_by', 'updated_by']),
            ],
            200,
        );
    }

    function getAvailableRoomsForCreateCourseDetail(Request $request, $id_session, $start_date, $end_date)
    {
        $user = $request->user;
        $id_days = $request->id_days;
        $user_id_perm = $this->permittedUser($user, [1, 10], $this->readOnly);

        return $this->availableRoomsForCreateCourseDetail($id_days, $id_session, $start_date, $end_date);
    }
    function getAvailableRoomsForUpdateCourseDetail(Request $request, $id_course_detail, $id_session, $start_date, $end_date)
    {
        $user = $request->user;
        $id_days = $request->id_days;
        $user_id_perm = $this->permittedUser($user, [1, 10], $this->readOnly);

        return $this->availableRoomsForUpdateCourseDetail($id_course_detail, $id_days, $id_session, $start_date, $end_date);
    }
}
