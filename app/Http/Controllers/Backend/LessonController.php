<?php

namespace App\Http\Controllers\Backend;

use App\Repositories\LessonRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class LessonController extends AppBaseController
{
    /** @var  LessonRepository */
    private $lessonRepository;

    public function __construct(LessonRepository $lessonRepo)
    {
        $this->lessonRepository = $lessonRepo;
		$this->lessonRepository->setPresenter("App\\Presenters\\LessonInListPresenter");
		parent::__construct();
    }

    /**
     * Display a listing of the Lesson.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
		$lessons = $this->lessonRepository->getLessons($request);
		$vars = [
			'page_title' => 'Lessons',
			'lessons' => $this->lessonRepository->presentResponse( $lessons )
		];

		return view('backend.lessons.index', $vars);
    }
}
