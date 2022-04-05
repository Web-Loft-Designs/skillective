<?php

namespace App\Http\Controllers\Backend;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Redirect;
use Auth;
use App\Repositories\UserRepository;
use App\Facades\ReportsBuilder;

class DashboardController extends Controller
{
	/** @var  UserRepository */
	private $userRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepo)
    {
		$this->userRepository = $userRepo;
        $this->middleware('auth');
		parent::__construct();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		// overview widget
		$overviewWidgetData = ReportsBuilder::getOverview();

		try{
			$request->request->add([
				'status'	=> 'active',
				'limit'		=> 5,
			]);
			$roleID = 2;
			$instructors = $this->userRepository->getUsers($request, $roleID);
			$this->userRepository->setPresenter("App\\Presenters\\InstructorsInListPresenter");
			$instructors = $this->userRepository->presentResponse($instructors);
		}catch (\Exception $e){
			Log::error('getInstructors : ' . $e->getMessage());
			$instructors = ['data'=>[]];
		}

		try{
			$request->request->add([
				'status'	=> 'active',
				'limit'		=> 5,
			]);
			$roleID = 3;
			$students = $this->userRepository->getUsers($request, $roleID);
			$this->userRepository->setPresenter("App\\Presenters\\StudentsInListPresenter");
			$students = $this->userRepository->presentResponse($students);
		}catch (\Exception $e){
			Log::error('getStudents : ' . $e->getMessage());
			$students = ['data'=>[]];
		}

        return view('backend.dashboard', [
            'page_title' => 'Dashboard',
			'instructors' => $instructors,
			'students' => $students,
			'overviewWidgetData' => $overviewWidgetData
        ]);
    }
}
