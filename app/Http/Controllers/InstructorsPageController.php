<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\InstructorsFilterRequest;
use App\Repositories\GenreRepository;
use App\Repositories\UserRepository;
use Log;
use Cookie;

class InstructorsPageController extends Controller
{
    public function index(InstructorsFilterRequest $request, UserRepository $userRepository, GenreRepository $genreRepository)
    {
//		try{
            $userRepository->setPresenter("App\\Presenters\\InstructorsInSearchListPresenter");
			$instructors = $userRepository->presentResponse($userRepository->getFilteredActiveInstructors($request));
//		}catch (\Exception $e){
//			Log::error('getFilteredActiveInstructors : ' . $e->getMessage());
//			$instructors = ['data'=>[]];
//		}

    	$vars = [
			'genres' => $genreRepository->presentResponse($genreRepository->getSiteInstructorsGenres())['data'],
			'instructors' => $instructors,
			'page_title' => 'Search Instructors'
		];
        return view('frontend.instructors', $vars);
    }
}
