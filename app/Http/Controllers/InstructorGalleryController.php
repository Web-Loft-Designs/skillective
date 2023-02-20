<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Repositories\GenreRepository;


class InstructorGalleryController extends Controller
{

    /**
     * @param GenreRepository $genreRepository
     * @return Application|Factory|View
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
