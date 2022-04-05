<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use App\Models\Profile;
use App\Repositories\UserRepository;
use Log;
use App\Repositories\GenreRepository;

class InstructorClientsController extends Controller
{
	/** @var  UserRepository */
	private $userRepository;
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, UserRepository $userRepository, GenreRepository $genreRepository)
    {
		try{
			$clients = $userRepository->presentResponse($userRepository->getInstructorClients(Auth::user()->id, $request));
		}catch (\Exception $e){
			Log::error('getInstructorClients : ' . $e->getMessage());
			$clients = ['data'=>[]];
		}

        $vars = [
            'page_title' => 'Clients',
            'clients' => $clients,
            'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
			'userGenres'	=> $genreRepository->presentResponse(Auth::user()->genres)['data'],
        ];

        return view('frontend.instructor.clients', $vars);
    }

}
