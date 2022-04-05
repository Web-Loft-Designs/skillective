<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Session;
use App\Repositories\BookingRepository;
use App\Repositories\GenreRepository;
use App\Repositories\LessonRequestRepository;
use App\Repositories\LessonRepository;
use Log;
use App\Models\Booking;

class InstructorBookingsController extends Controller
{
    /** @var  LessonRequestRepository */
    private $lessonRequestRepository;

    public function __construct(LessonRequestRepository $lessonRequestRepository)
    {
        $this->lessonRequestRepository = $lessonRequestRepository;
        parent::__construct();
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(BookingRepository $bookingRepository, GenreRepository $genreRepository, LessonRepository $lessonRepo, Request $request)
    {
		// try{
		// 	if (!$request->has('type'))
		// 		$request->request->add(['type'	=> 'pending']);
		// 	if ($request->filled('booking')){
		// 		$request->request->add(['booking' => $request->input('booking')]);
		// 		// redeclare type
		// 		$request->request->add(['type'	=> 'pending_cancellation']);

		// 		$bookingToShow = $bookingRepository->findWithoutFail($request->input('booking'));
		// 		if (!$bookingToShow)
		// 			abort(404);
		// 	}
        //     if ($request->get('type') == 'lesson_requests'){
        //         $this->lessonRequestRepository->setPresenter("App\\Presenters\\LessonRequestInListPresenter");
        //         $bookings = $this->lessonRequestRepository->presentResponse($this->lessonRequestRepository->getUserLessonRequests($request, Auth::user()->id));
        //     }else{
        //         $bookingRepository->setPresenter("App\\Presenters\\BookingInListPresenter");
        //         $bookings = $bookingRepository->presentResponse($bookingRepository->getInstructorBookings($request, Auth::user()->id));
        //     }
		// }catch (\Exception $e){
        //     Log::info('test');
		// 	Log::error('getInstructorBookings : ' . $e->getMessage());
		// 	$bookings = ['data'=>[]];
        // }
        

        try{
			$request = new Request([
				'limit'	=> 20,
				'type' => 'all',
				'sort' => 'date'
			]);
 			
			$lessonRepo->setPresenter("App\\Presenters\\LessonDashboardPresenter");
			
			$lessons = $lessonRepo->presentResponse($lessonRepo->getDashboardInstructorLessons($request, Auth::user()->id));

		}catch (\Exception $e){
			
			Log::error('getInstructorBookings : ' . $e->getMessage());
			$lessons = ['data'=>[]];
        }


		$vars = [
			'page_title' => 'Bookings',
			'bookings' => $lessons,
            'siteGenres'	=> $genreRepository->presentResponse($genreRepository->getSiteGenres())['data'],
            'userGenres'	=> $genreRepository->presentResponse(Auth::user()->genres)['data']
        ];
        

        

		return view('frontend.instructor.bookings', $vars);
    }
}
