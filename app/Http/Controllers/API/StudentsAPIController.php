<?php

namespace App\Http\Controllers\API;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;

/**
 * Class UserController
 * @package App\Http\Controllers\API
 */

class StudentsAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the User.
     * GET|HEAD /users
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
		try{
			$students = $this->userRepository->presentResponse($this->userRepository->getStudents($request, Auth::user()->id));
		}catch (\Exception $e){
			Log::error('getStudents : ' . $e->getMessage());
			$students = ['data'=>[]];
		}

        return $this->sendResponse($students);
    }
}
