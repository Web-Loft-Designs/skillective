<?php

namespace App\Http\Controllers\API\Backend;


use App\Repositories\LessonRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Prettus\Repository\Exceptions\RepositoryException;


class LessonsAPIController extends AppBaseController
{
    /** @var  LessonRepository */
    private $lessonRepository;

    public function __construct(LessonRepository $lessonRepo)
    {
        parent::__construct();
        $this->lessonRepository = $lessonRepo;
		$this->lessonRepository->setPresenter("App\\Presenters\\LessonInListPresenter");
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function index(Request $request)
    {
		$lessons = $this->lessonRepository->presentResponse( $this->lessonRepository->getLessons($request) );
        return $this->sendResponse($lessons);
    }
}
