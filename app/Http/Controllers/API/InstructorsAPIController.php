<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class InstructorsAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        parent::__construct();
        $this->userRepository = $userRepo;
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $instructors = $this->userRepository->getInstructors($request, Auth::user()->id);
            $instructors = $this->userRepository->presentResponse($instructors);
        } catch (\Exception $e) {
            Log::error('getInstructors : ' . $e->getMessage());
            $instructors = ['data' => []];
        }

        return $this->sendResponse($instructors);
    }

    /**
     * @return JsonResponse
     */
    public function getFeaturedInstructors()
    {
        $featuredInstructors = $this->userRepository->presentResponse($this->userRepository->getInstructorsForHome());

        return $this->sendResponse($featuredInstructors);
    }


    /**
     * @param User $instructor
     * @return JsonResponse
     */
    public function getRelationInstructors(User $instructor)
    {
        $related = $this->userRepository->getRelationInstructors($instructor);
        return $this->sendResponse($related);

    }
}
