<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Repositories\GenreRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InstructorClientsController extends Controller
{
	/** @var  UserRepository */
	private $userRepository;

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param GenreRepository $genreRepository
     * @return Application|Factory|View
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
