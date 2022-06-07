<?php

namespace App\Http\Controllers;

use App\Repositories\LessonRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Repositories\GenreRepository;
use App\Repositories\TestimonialRepository;
use Cookie;
use Illuminate\Support\Facades\Redis;
use Vinkla\Instagram\Instagram;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Booking;
use Carbon\Carbon;
use Auth;

class HomeController extends AppBaseController
{
    public function index(GenreRepository $genreRepository, TestimonialRepository $testimonialRepository, LessonRepository $lessonRepository, UserRepository $userRepository)
    {
//        getLocationDetails('17425 Benson Rd Cottonwood, California(CA), 96022');
		$userIpLocation = '';
//		$geoLocation = geoip(request()->ip());
//		if ($geoLocation instanceof \Torann\GeoIP\Location && $geoLocation->getAttribute('country')=='United States'){
//			$userIpLocation = $geoLocation->getAttribute('city') . ', ' . $geoLocation->getAttribute('state') . ', USA';
//		}

		if (!$this->currentPage){
			$this->currentPage = getCurrentPage('/');
			view()->share( 'currentPage', $this->currentPage );
		}
//        dd(getLocationDetails('1375 Big Orange Road, Cordova, TN, USA'));
    	$vars = [
			'siteGenres' => $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
            'siteInstructorsGenres' => $genreRepository->presentResponse($genreRepository->getSiteInstructorsGenres())['data'],
			'featuredGenres' => $genreRepository->presentResponse($genreRepository->getFeatured(9))['data'],
			'featuresInstructors' => $userRepository->presentResponse($userRepository->getInstructorsForHome())['data'],
			'testimonials' => $testimonialRepository->orderBy('position', 'asc')->all(),
			'upcomingNearbyLessons' => $lessonRepository->upcomingNearbyLessons(request()->ip()),
			'userIpLocation' => $userIpLocation,
            'userGenres' => Auth::check() ? Auth::user()->genres : [],
		];
        return view('home', $vars);
    }
}
