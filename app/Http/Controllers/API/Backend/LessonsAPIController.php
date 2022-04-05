<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\Lesson;
use App\Http\Requests\API\CancelLessonsAPIRequest;
use App\Models\User;
use App\Repositories\LessonRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;


class LessonsAPIController extends AppBaseController
{
    /** @var  LessonRepository */
    private $lessonRepository;

    public function __construct(LessonRepository $lessonRepo)
    {
        $this->lessonRepository = $lessonRepo;
		$this->lessonRepository->setPresenter("App\\Presenters\\LessonInListPresenter");
    }

    public function index(Request $request)
    {
		$lessons = $this->lessonRepository->presentResponse( $this->lessonRepository->getLessons($request) );

        return $this->sendResponse($lessons);
    }
}
