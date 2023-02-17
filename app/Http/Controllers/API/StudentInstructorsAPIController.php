<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\AddStudentInstructorsAPIRequest;
use App\Http\Requests\API\RemoveStudentInstructorsAPIRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class StudentInstructorsAPIController extends AppBaseController
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
			$this->userRepository->setPresenter("App\\Presenters\\StudentInstructorInListPresenter");
			$instructors = $this->userRepository->presentResponse($this->userRepository->getStudentInstructors(Auth::user()->id, $request));
		}catch (\Exception $e){
			Log::error('getStudentInstructors : ' . $e->getMessage());
			$instructors = ['data'=>[]];
		}

        return $this->sendResponse($instructors);
    }


    /**
     * @param AddStudentInstructorsAPIRequest $request
     * @return JsonResponse
     */
    public function add(AddStudentInstructorsAPIRequest $request)
    {
    	$count_added = 0;
        $user = $this->userRepository->find($request->required);
		foreach ($request->input('instructors') as $instructorId){
			$instructor = $this->userRepository->find($instructorId);
			$instructor->clients()->syncWithoutDetaching( $user->id );
			if ($instructor->hasRole($this->userRepository->model()::ROLE_INSTRUCTOR) && !$user->hasOwnInstructor($instructorId)) {
                $user->instructors()->attach( $instructorId );
				$count_added++;
			}
		}

        return $this->sendResponse(true, $count_added . ' instructors added');
    }


    /**
     * @param $id
     * @return JsonResponse
     */
    public function remove($id)
    {
        $instructor = $this->userRepository->findWithoutFail($id);

        if (empty($instructor)) {
            return $this->sendError('Instructor not found');
        }

        Auth::user()->instructors()->detach($id);

        return $this->sendResponse(true, 'Instructor deleted');
    }

    /**
     * @param RemoveStudentInstructorsAPIRequest $request
     * @return JsonResponse
     */
    public function removeMany(RemoveStudentInstructorsAPIRequest $request)
	{
		$count_removed = 0;
		foreach ($request->input('instructors') as $instructorId){
			Auth::user()->instructors()->detach($instructorId);
			$count_removed++;
		}

		return $this->sendResponse(true, $count_removed . ' instructors removed');
	}


    /**
     * @param User $instructor
     * @param $student
     * @return JsonResponse
     */
    public function addAndMarkAsFavorite(User $instructor, $student = [])
	{

        $student = $student ? $student : Auth::user();
        $instructorId = $instructor->id;

		/** @var User $instructor */
		$instructor = $this->userRepository->findWithoutFail($instructorId);

		if (empty($instructor)) {
			return $this->sendError('Instructor not found');
		}
		if ($instructor->hasRole($this->userRepository->model()::ROLE_INSTRUCTOR)) {
            if (!$student->hasOwnInstructor($instructorId))
            {
                $student->instructors()->attach($instructorId, ['is_favorite' => true]);
            }else{
                $student->instructors()->updateExistingPivot($instructorId, ['is_favorite' => true], false);
            }
		}

		return $this->sendResponse(true, 'Instructor added to your favorites');
	}


    /**
     * @param $instructorId
     * @return JsonResponse
     */
    public function removeFromFavorites($instructorId)
	{
		/** @var User $instructor */
		$instructor = $this->userRepository->findWithoutFail($instructorId);

		if (empty($instructor)) {
			return $this->sendError('Instructor not found');
		}
		if ($instructor->hasRole($this->userRepository->model()::ROLE_INSTRUCTOR)) {
			if (Auth::user()->hasOwnInstructor($instructorId)){
				Auth::user()->instructors()->updateExistingPivot( $instructorId, ['is_favorite' => false], false );
			}
		}

		return $this->sendResponse(true, 'Instructor updated');
	}

    /**
     * @param Request $request
     * @param User $instructor
     * @return JsonResponse
     */
    public function enableGeoNotifications(Request $request, User $instructor)
	{
        $instructor = $this->userRepository->findWithoutFail($instructor->id);
        if( $instructor )
        {
            if ($instructor->hasRole($this->userRepository->model()::ROLE_INSTRUCTOR)) {
                if (!Auth::user()->hasOwnInstructor($instructor->id))
                    Auth::user()->instructors()->attach( $instructor->id, ['geo_notifications_allowed' => true] );
                else // enable just geo_notifications_allowed
                    Auth::user()->instructors()->updateExistingPivot( $instructor->id, ['geo_notifications_allowed' => true], false );
            }
        }

		return $this->sendResponse(true, 'Geo Notifications enabled for this Instructor');
	}

    /**
     * @param $instructorId
     * @return JsonResponse
     */
    public function disableGeoNotifications($instructorId)
	{
		/** @var User $instructor */
		$instructor = $this->userRepository->findWithoutFail($instructorId);

		if (empty($instructor)) {
			return $this->sendError('Instructor not found');
		}
		if ($instructor->hasRole($this->userRepository->model()::ROLE_INSTRUCTOR)) {
			if (Auth::user()->hasOwnInstructor($instructorId)){
				Auth::user()->instructors()->updateExistingPivot( $instructorId, ['geo_notifications_allowed' => false], false );
			}
		}

		return $this->sendResponse(true, 'Geo Notifications disabled for this Instructor');
	}

    /**
     * @param User $instructor
     * @return JsonResponse
     */
    public function enableVirtualLessonNotifications(User $instructor)
    {
        $instructor = $this->userRepository->findWithoutFail($instructor->id);

        if ($instructor->hasRole($this->userRepository->model()::ROLE_INSTRUCTOR)) {
            if (!Auth::user()->hasOwnInstructor($instructor->id))
                Auth::user()->instructors()->attach( $instructor->id, ['virtual_notifications_allowed' => true] );
            else // enable just geo_notifications_allowed
                Auth::user()->instructors()->updateExistingPivot( $instructor->id, ['virtual_notifications_allowed' => true], false );
        }

        return $this->sendResponse(true, 'Virtual Lesson Notifications enabled for this Instructor');
    }

    /**
     * @param $instructorId
     * @return JsonResponse
     */
    public function disableVirtualLessonNotifications($instructorId)
    {
        /** @var User $instructor */
        $instructor = $this->userRepository->findWithoutFail($instructorId);

        if (empty($instructor)) {
            return $this->sendError('Instructor not found');
        }
        if ($instructor->hasRole($this->userRepository->model()::ROLE_INSTRUCTOR)) {
            if (Auth::user()->hasOwnInstructor($instructorId)){
                Auth::user()->instructors()->updateExistingPivot( $instructorId, ['virtual_notifications_allowed' => false], false );
            }
        }

        return $this->sendResponse(true, 'Virtual Lesson Notifications disabled for this Instructor');
    }
}
