<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Genre;
use DB;
use Carbon\Carbon;
use Log;

class SearchAPIController extends AppBaseController
{
    public function autocompleteInstructor(Request $request)
    {
        $searchString = $request->input('instructor');

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

        $result = Genre::where('title', 'LIKE', $searchString . '%')
            ->get();


        return $this->sendResponse($result);
    }

    public function autocompleteLocations(Request $request)
    {

        $searchString = $request->input('location');

        if (!$searchString) {
            return $this->sendError("genre query param is requrid", 400);
        }

        $nowOnServer = Carbon::now()->format('Y-m-d H:i:s');

        $result = DB::table('lessons')->select(["city", DB::raw("count(city) as city_count")])->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) <= lessons.start")->where('lesson_type', 'in_person')->where('city', 'LIKE',  $searchString . '%')->groupBy('city')->get();


        $result = json_decode(json_encode($result), true);

        $instructorsCity = User::leftJoin("profiles", 'users.id', '=', "profiles.user_id")
            ->select(['users.id', 'profiles.city', 'profiles.user_id'])
            ->whereHas(
                'roles',
                function ($q) {
                    $q->where('name', USER::ROLE_INSTRUCTOR);
                }
            )
            ->where('status', 'active')
            ->where('profiles.city', 'LIKE', '%' . $searchString . '%')
            ->get();


        $instructorsCity = json_decode(json_encode($instructorsCity), true);



        foreach ($instructorsCity as $key => $value) {


            $citys = array_column($result, 'city');


            $isExist = array_search($value['profile']['city'], $citys);

            if ($isExist === 0 || $isExist > 0) {
            } else {

                array_push($result, array('city' => $value['profile']['city'], 'city_count' => 0));
            }
        }



        // TODO
        // Use another function to get users from city count

        foreach ($result as $key => $value) {

            $instrucotrsCount = User::leftJoin("profiles", 'users.id', '=', "profiles.user_id")
                ->select(['users.id', 'profiles.city', 'profiles.user_id'])
                ->whereHas(
                    'roles',
                    function ($q) {
                        $q->where('name', USER::ROLE_INSTRUCTOR);
                    }
                )
                ->where('status', 'active')
                ->where('profiles.city', $value['city'])
                ->groupBy('profiles.city')
                ->count();

            $result[$key]['instructors_count'] = $instrucotrsCount;
        }

        return $this->sendResponse($result);
    }
}
