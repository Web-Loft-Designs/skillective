<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CreateProfileRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Log;

class StudentController extends AppBaseController
{

	/** @var  UserRepository */
	private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
		parent::__construct();
    }

    /**
     * Display a listing of the User.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
		try{
			if (!$request->has('status'))
				$request->request->add([
					'status'	=> 'active'
				]);
			$roleID = Role::findByName(User::ROLE_STUDENT)->id;
			$this->userRepository->setPresenter("App\\Presenters\\StudentsInListPresenter");
			$students = $this->userRepository->presentResponse($this->userRepository->getUsers($request, $roleID));
		}catch (\Exception $e){
			Log::error('getStudents : ' . $e->getMessage());
			$students = ['data'=>[]];
		}

		return view('backend.students.index', [
			'students' => $students,
			'page_title' => "Students",
		]);
    }
}
