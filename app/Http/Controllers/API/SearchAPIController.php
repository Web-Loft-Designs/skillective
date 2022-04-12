<?php

namespace App\Http\Controllers\API;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
        $searchString = trim($searchString, '@');

        if (!$searchString) {
            return $this->sendError("instructor query param is requrid", 400);
        }

        $searchStringArr = preg_split('/\s+/', $searchString, -1, PREG_SPLIT_NO_EMPTY);

        $userQuery = User::with(['profile', 'roles']);

        foreach($searchStringArr as $searchString) {
            $userQuery->searchFromNameInstagram($searchString);
        }

        $result = $userQuery->whereHas('roles', static function (Builder $query) {
                $query->where('name', USER::ROLE_INSTRUCTOR);
            })
            ->get();

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

        $countInstructorLive = User::where('status', 'active')
            ->whereHas('roles', function ($q) {
                $q->where('name', USER::ROLE_INSTRUCTOR);
            })
            ->whereHas('profile', function ($q) use ($searchString) {
                $q->where('city', 'LIKE', '%' . $searchString . '%');
            })
            ->count();

        $countInstructorWork = User::where('status', 'active')
            ->whereHas('roles', function ($q) {
                $q->where('name', USER::ROLE_INSTRUCTOR);
            })
            ->whereHas('lessons', function ($q) use ($searchString) {
                $q->where('city', 'LIKE', '%' . $searchString . '%');
            })
            ->count();

        return $this->sendResponse([
            'city' => $searchString,
            'city_count' => $countLessons,
            'instructors_count' => $countInstructorLive + $countInstructorWork,
        ]);
    }
}
