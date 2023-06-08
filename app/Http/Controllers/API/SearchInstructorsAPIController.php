<?php

namespace App\Http\Controllers\API;

use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\InstructorsFilterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;


class SearchInstructorsAPIController extends AppBaseController
{
    /**
     * @param InstructorsFilterRequest $request
     * @param UserRepository $userRepository
     * @return JsonResponse
     */
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
