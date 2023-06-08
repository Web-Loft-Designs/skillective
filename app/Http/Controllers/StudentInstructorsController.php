<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class StudentInstructorsController extends Controller
{

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Application|Factory|View
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
