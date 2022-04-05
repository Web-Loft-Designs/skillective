<?php

namespace App\Http\Controllers\API;

use App\Facades\UserRegistrator;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\StudentRegisterRequest;
use App\Http\Requests\StudentAddClientListRequest;
use App\Http\Requests\StudentCreateClientListRequest;
use App\Http\Requests\StudentSmallRegisterRequest;
use App\Repositories\StudentAddClientListRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class StudentAddClientListAPIController extends AppBaseController
{
    private $addClientListRepository;
    private $userRepository;

    public function __construct(StudentAddClientListRepository $addClientListRepository, UserRepository $userRepository)
    {
        $this->addClientListRepository = $addClientListRepository;
        $this->userRepository = $userRepository;
        parent::__construct();
    }

    public function addToClientList(StudentAddClientListRequest $request)
    {
        $student = Auth::user();
        return $this->toClientList($request, $student);
    }

    public function createToClientList(StudentSmallRegisterRequest $request)
    {
        $student = UserRegistrator::registerInactiveStudent($request);
        return $this->toClientList($request, $student);
    }

    private function toClientList($request, $student)
    {
        if(empty($student)) {
            return $this->sendResponse(false, 'Client not found');
        }

        $student = $this->userRepository->find($student->id);

        if ($student->hasRole($this->userRepository->model()::ROLE_STUDENT)) {
            $instructor = $this->userRepository->find($request->instructor_id);
            $instructor->clients()->syncWithoutDetaching($student->id);
            $instructorGenresIds = $instructor->genres->pluck('id');
            $instructorsFromGenres = $this->userRepository->getInstructorFromGenres($instructorGenresIds)->toArray();

            $stdunInstrApi = new StudentInstructorsAPIController($this->userRepository);
            $stdunInstrApi->addAndMarkAsFavorite($instructor->id);

            $message = 'Client ' . $student->getName() . ' added to instructor ' . $instructor->getName();
            return $this->sendResponse($instructorsFromGenres, $message);
        }

        return $this->sendError('Client not has true role');
    }
}
