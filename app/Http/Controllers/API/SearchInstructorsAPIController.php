<?php

namespace App\Http\Controllers\API;

use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\InstructorsFilterRequest;
use Response;
use Log;

class SearchInstructorsAPIController extends AppBaseController
{
    public function index(InstructorsFilterRequest $request, UserRepository $userRepository)
    {
        try{
            $userRepository->setPresenter("App\\Presenters\\InstructorsInSearchListPresenter");
            $instructors = $userRepository->presentResponse($userRepository->getFilteredActiveInstructors($request));
        }catch (\Exception $e){
            Log::error('getFilteredActiveInstructors : ' . $e->getMessage());
            $instructors = ['data'=>[]];
        }

        return $this->sendResponse($instructors);
    }
}
