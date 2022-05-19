<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;

/**
 * Class UserController
 * @package App\Http\Controllers\API
 */

class InstructorsAPIController extends AppBaseController
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
        try {
            $instructors = $this->userRepository->getInstructors($request, Auth::user()->id);
            $instructors = $this->userRepository->presentResponse($instructors);
        } catch (\Exception $e) {
            Log::error('getInstructors : ' . $e->getMessage());
            $instructors = ['data' => []];
        }

        return $this->sendResponse($instructors);
    }


    public function getFeaturedInstructors()
    {
        $featuredInstructors = $this->userRepository->presentResponse($this->userRepository->getInstructorsForHome());

        return $this->sendResponse($featuredInstructors);
    }

    /**
     * @param User $instructor
     * @return void
     */
    public function getRelationInstructors(User $instructor)
    {

        $related = $this->userRepository->getRelationInstructors($instructor);

        return $this->sendResponse($related);

    }

}
