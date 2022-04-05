<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use App\Repositories\UserRepository;
use Log;

class StudentInstructorsController extends Controller
{
	/** @var  UserRepository */
	private $userRepository;
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, UserRepository $userRepository)
    {
		try{
			$userRepository->setPresenter("App\\Presenters\\StudentInstructorInListPresenter");
			$instructors = $userRepository->presentResponse($userRepository->getStudentInstructors(Auth::user()->id, $request));
		}catch (\Exception $e){
			Log::error('getStudentInstructors : ' . $e->getMessage());
			$instructors = ['data'=>[]];
		}

        $vars = [
            'page_title' => 'Instructors',
			'instructors' => $instructors
        ];

        return view('frontend.student.instructors', $vars);
    }

}
