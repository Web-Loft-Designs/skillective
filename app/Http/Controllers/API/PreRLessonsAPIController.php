<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\PreRecordedLesson;
use Illuminate\Http\Request;
use App\Repositories\PreRLessonRepository;


class PreRLessonsAPIController extends AppBaseController
{
    private $preRLessonRepository;

    public function __construct(PreRLessonRepository $preRLessonRepo)
    {
        $this->preRLessonRepository = $preRLessonRepo;
    }

    public function index(Request $request)
    {
        $lessons = $this->preRLessonRepository->getPreRLessons($request);

        $this->preRLessonRepository->setPresenter("App\\Presenters\\PreRLessonInListPresenter");
        $lessons = $this->preRLessonRepository->presentResponse( $lessons );
        return $this->sendResponse($lessons);
    }

    public function getPreRecordedLessonsByInstructorId(Request $request, $instructor)
    {
        $instructorLessons = PreRecordedLesson::where('instructor_id', "=", (int) $instructor)
            ->orderBy('pre_r_lessons.created_at', 'desc')
            ->paginate(20);

        $this->preRLessonRepository->setPresenter("App\\Presenters\\PreRLessonInListPresenter");
        $instructorLessons = $this->preRLessonRepository->presentResponse( $instructorLessons );
        return $this->sendResponse($instructorLessons);
    }
}
