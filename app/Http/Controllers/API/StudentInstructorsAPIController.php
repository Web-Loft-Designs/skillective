<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\AddStudentInstructorsAPIRequest;
use App\Http\Requests\API\RemoveStudentInstructorsAPIRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Response;
use Log;


class StudentInstructorsAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the Instructor.
     * GET|HEAD /instructors
     *
     * @param Request $request
     * @return Response
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
     * Store a newly created Instructor in storage.
     * POST /instructors
     *
     * @param AddInstructorsAPIRequest $request
     *
     * @return Response
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
     * Remove the specified Instructor from storage.
     * DELETE /instructors/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function remove($id)
    {
        /** @var Instructor $instructor */
        $instructor = $this->userRepository->findWithoutFail($id);

        if (empty($instructor)) {
            return $this->sendError('Instructor not found');
        }

        Auth::user()->instructors()->detach($id);

        return $this->sendResponse(true, 'Instructor deleted');
    }

	public function removeMany(RemoveStudentInstructorsAPIRequest $request)
	{
		$count_removed = 0;
		foreach ($request->input('instructors') as $instructorId){
			Auth::user()->instructors()->detach($instructorId);
			$count_removed++;
		}

		return $this->sendResponse(true, $count_removed . ' instructors removed');
	}



	public function addAndMarkAsFavorite($student, $instructorId)
	{
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

	public function enableGeoNotifications(Request $request)
	{
		$instructors = (array) $request->instructor;

        if( $instructors )
        {

            foreach ( $instructors as $item )
            {

                /** @var User $instructor */
                $instructor = $this->userRepository->findWithoutFail($item);

                if( $instructor )
                {

                    if ($instructor->hasRole($this->userRepository->model()::ROLE_INSTRUCTOR)) {
                        if (!Auth::user()->hasOwnInstructor($item))
                            Auth::user()->instructors()->attach( $item, ['geo_notifications_allowed' => true] );
                        else // enable just geo_notifications_allowed
                            Auth::user()->instructors()->updateExistingPivot( $item, ['geo_notifications_allowed' => true], false );
                    }

                }

            }

        }else{

            return $this->sendError('Instructor not found');

        }

		return $this->sendResponse(true, 'Geo Notifications enabled for this Instructor');

	}

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

    public function enableVirtualLessonNotifications(Request $request)
    {
        $instructors = (array) $request->instructor;

        if( $instructors )
        {

            foreach ( $instructors as $item )
            {

                /** @var User $instructor */
                $instructor = $this->userRepository->findWithoutFail($item);

                if ($instructor->hasRole($this->userRepository->model()::ROLE_INSTRUCTOR)) {
                    if (!Auth::user()->hasOwnInstructor($item))
                        Auth::user()->instructors()->attach( $item, ['virtual_notifications_allowed' => true] );
                    else // enable just geo_notifications_allowed
                        Auth::user()->instructors()->updateExistingPivot( $item, ['virtual_notifications_allowed' => true], false );
                }

            }

        }else{
            return $this->sendError('Instructor not found');
        }

        return $this->sendResponse(true, 'Virtual Lesson Notifications enabled for this Instructor');
    }

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
