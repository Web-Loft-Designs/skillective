<?php

namespace App\Http\Controllers\API;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Genre;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SearchAPIController extends AppBaseController
{
    public function autocompleteInstructor(Request $request)
    {
        $searchString = $request->input('instructor');

        if (!$searchString) {
            return $this->sendError("instructor query param is requrid", 400);
        }

        $result = User::leftJoin("profiles", 'users.id', '=', "profiles.user_id")
            ->where(function ($query) use ($searchString) {
                $query->where('profiles.instagram_handle', 'LIKE', $searchString . '%');
                $query->orWhere('first_name', 'LIKE', $searchString . '%');
                $query->orWhere('last_name', 'LIKE', $searchString . '%');
            })
            ->whereHas(
                'roles',
                function ($q) {
                    $q->where('name', USER::ROLE_INSTRUCTOR);
                }
            )->with(['roles'])->get();

        return $this->sendResponse($result);
    }


    public function autocompleteGenres(Request $request)
    {
        $searchString = $request->input('genre');

        if (!$searchString) {
            return $this->sendError("genre query param is requrid", 400);
        }

        $result = Genre::where('title', 'LIKE', $searchString . '%')->get();

        return $this->sendResponse($result);
    }

    public function autocompleteLocations(Request $request)
    {
        $searchString = $request->input('location');
        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s');

        if (!$searchString) {
            return $this->sendError("location query param is requrid", 400);
        }

        $countLessons = Lesson::where('lesson_type', 'in_person')
            ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) <= lessons.start")
            ->where('city', 'LIKE',  $searchString . '%')
            ->groupBy('city')
            ->count();

        $countInstructor = User::where('status', 'active')
            ->whereHas('roles', function ($q) {
                $q->where('name', USER::ROLE_INSTRUCTOR);
            })
            ->where(function($q) use ($searchString) {
                $q->whereHas('profile', function ($q) use ($searchString) {
                    $q->where('city', 'LIKE', '%' . $searchString . '%');
                })
                ->orWhereHas('lessons', function ($q) use ($searchString) {
                    $q->where('city', 'LIKE', '%' . $searchString . '%');
                });
            })
            ->groupBy('id')
            ->count();

        dump('Lesson: ' . $countLessons);
        dd('Instruct: ' . $countInstructor);

        return $this->sendResponse([
            'city' => $searchString,
            'city_count' => $countLessons,
            'instructors_count' => $countInstructorLive,
        ]);
    }
}
