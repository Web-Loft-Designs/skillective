<?php

namespace App\Http\Controllers\API;

use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class StudentsAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
        parent::__construct();
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
		try{
			$students = $this->userRepository
                ->presentResponse($this->userRepository->getStudents($request, Auth::user()->id));
		}catch (\Exception $e){
			Log::error('getStudents : ' . $e->getMessage());
			$students = ['data'=>[]];
		}
        return $this->sendResponse($students);
    }
}
