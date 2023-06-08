<?php

namespace App\Http\Controllers\API;

use App\Facades\UserRegistrator;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\StudentAddClientListRequest;
use App\Http\Requests\StudentSmallRegisterRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;


class StudentAddClientListAPIController extends AppBaseController
{

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    /**
     * @param StudentAddClientListRequest $request
     * @return JsonResponse
     */
    public function addToClientList(StudentAddClientListRequest $request)
    {
        $student = $this->userRepository->find($request->required);
        return $this->toClientList($request, $student);
    }

    /**
     * @param StudentSmallRegisterRequest $request
     * @return mixed
     */
    public function createToClientList(StudentSmallRegisterRequest $request)
    {
        $student = UserRegistrator::registerInactiveStudent($request);
        $this->toClientList($request, $student);
        return $student;
    }

    /**
     * @param $request
     * @param $student
     * @return JsonResponse
     */
    private function toClientList($request, $student)
    {
        if(empty($student)) {
            return $this->sendResponse(false, 'Client not found');
        }

        $student = $this->userRepository->find($student->id);

        if ($student->hasRole($this->userRepository->model()::ROLE_STUDENT)) {
            $instructors = (array) $request->instructor_id;
            $message = null;
            $instructorGenresIds = new Collection();

            foreach ( $instructors as $item )
            {

                $instructor = $this->userRepository->find($item);
                $instructor->clients()->syncWithoutDetaching($student->id);
                $instructorGenresIds->push($instructor->genres->pluck('id')->toArray());

                $stdunInstrApi = new StudentInstructorsAPIController($this->userRepository);
                $stdunInstrApi->addAndMarkAsFavorite($instructor, $student);

                $message .= 'Client ' . $student->getName() . ' added to instructor ' . $instructor->getName();

            }

            $instructorsFromGenres = $this->userRepository->getInstructorFromGenres($instructorGenresIds->collapse())->toArray();

            return $this->sendResponse($instructorsFromGenres, $message);
        }

        return $this->sendError('Client not has true role');
    }
}
