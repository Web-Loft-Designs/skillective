<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use Laravel\Socialite\Facades\Socialite;
use App\Repositories\GenreRepository;
use App\Repositories\UserRepository;

class InstructorGalleryController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GenreRepository $genreRepository)
    {
        $vars = [
            'page_title' => 'Gallery',
            'userMedia'	=> Auth::user()->getGalleryMedia(),
            'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
			'userGenres'	=> $genreRepository->presentResponse(Auth::user()->genres)['data'],
			'update_media_from_instagram_url' => Socialite::driver('ig')->with(['redirect_uri' => route('social.instagram.media.update')])->redirect()->getTargetUrl()
        ];

        return view('frontend.instructor.gallery', $vars);
    }

}
