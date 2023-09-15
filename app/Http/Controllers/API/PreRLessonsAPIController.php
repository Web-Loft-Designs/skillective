<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Models\PreRecordedLesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Repositories\PreRLessonRepository;
use Prettus\Repository\Exceptions\RepositoryException;


class PreRLessonsAPIController extends AppBaseController
{
    /**
     * @var PreRLessonRepository
     */
    private PreRLessonRepository $preRLessonRepository;

    public function __construct(PreRLessonRepository $preRLessonRepo)
    {
        parent::__construct();
        $this->preRLessonRepository = $preRLessonRepo;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function index(Request $request): JsonResponse
    {
        $lessons = $this->preRLessonRepository->getPreRLessons($request);
        $this->preRLessonRepository->setPresenter("App\\Presenters\\PreRLessonInListPresenter");
        $lessons = $this->preRLessonRepository->presentResponse( $lessons );
        return $this->sendResponse($lessons);
    }

    /**
     * @param Request $request
     * @param $instructor
     * @return JsonResponse
     */
    public function getPreRecordedLessonsByInstructorId(Request $request, $instructor): JsonResponse
    {
        $instructorLessons = PreRecordedLesson::where('instructor_id', "=", (int) $instructor)
            ->orderBy('pre_r_lessons.created_at', 'desc')
            ->paginate(20);

        $this->preRLessonRepository->setPresenter("App\\Presenters\\PreRLessonInListPresenter");
        $instructorLessons = $this->preRLessonRepository->presentResponse( $instructorLessons );
        return $this->sendResponse($instructorLessons);
    }
}
