<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreatePreRLessonAPIRequest;
use App\Models\PreRecordedLesson;
use App\Models\PreRLessonFile;
use Illuminate\Http\Request;
use App\Repositories\PreRLessonRepository;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\CreateBlockBlobOptions;
use Log;

use Auth;
use getID3;

class PreRLessonsInstructorAPIController extends AppBaseController
{
    private $preRLessonRepository;

    public function __construct(PreRLessonRepository $preRLessonRepo)
    {
        $this->preRLessonRepository = $preRLessonRepo;
    }

    public function index(Request $request)
    {

        $lessons = $this->preRLessonRepository->getInstructorPreRLessons($request);

        $this->preRLessonRepository->setPresenter("App\\Presenters\\PreRLessonInListPresenter");
        $instructorLessons = $this->preRLessonRepository->presentResponse($lessons);
        return $this->sendResponse($instructorLessons);
    }

    public function store(CreatePreRLessonAPIRequest $request)
    {

        if (!Auth::user()->canAddNewLesson()) {
            return $this->sendError('To add new lesson you must connect a submerchant account to your profile, upload profile image and have at least one media item in gallery', 400);
        }

        $user = Auth::user();

        $data = $request->toArray();
        $data['instructor_id'] = $user->id;
        $data['genre_id'] = $data['genre'];
        unset($data['genre']);

        if ($data['video']) {
            $file_path = storage_path('app/public/videos/' . $user->id . '/' . $data['video']);

            $getID3 = new getID3;
            $file = $getID3->analyze($file_path);

            $data['duration'] = $file['playtime_string'];

            if (strlen($data['duration']) <= 5) {
                $data['duration'] = '00:' . $data['duration'];
            }

//            $content = fopen($file_path, "r");
//
//            $connectionString = "DefaultEndpointsProtocol=https;AccountName=skillective;AccountKey=nrKEkM7ihgQbuybiz/8NzsJJCdpwKQaxmmERc/F5x9kEXl/5RiXMet/5Mzw7QrA3jCdqUUd/9ETamRFatcheIg==;EndpointSuffix=core.windows.net";
//
//            $contentType = "video/mp4";
//            $options = new CreateBlockBlobOptions();
//            $options->setContentType($contentType);
//
//            try {
//
//                $blobClient = BlobRestProxy::createBlobService($connectionString);
//                $blobClient->createBlockBlob("public",  $data['video'], $content, $options);
//                echo "success";
//            } catch (ServiceException $e) {
//                $this->sendError($e);
//            }
        }
        $preRecordedLesson = PreRecordedLesson::create($data);

        $documents = $request->input('documents', null);

        $documentsCount = count($documents);

        if ($documentsCount > 0) {
            foreach ($documents as $key => $name) {
                PreRLessonFile::create(array('pre_r_lesson_id' => $preRecordedLesson->id, 'name' => $name));
            }
        }

        return $this->sendResponse('Lesson created');
    }

    public function getInstructorLessonById(Request $request, $lesson)
    {
        $lesson = $this->preRLessonRepository->findWithoutFail((int)$lesson);

        if (empty($lesson)) {
            return $this->sendError('Lesson not found');
        }

        $this->preRLessonRepository->setPresenter("App\\Presenters\\PreRLessonSinglePresenter");
        $lesson = $this->preRLessonRepository->presentResponse($lesson);
        return $this->sendResponse($lesson);
    }

    public function getAvailableGenres(Request $request)
    {

        $instructorGenres = PreRecordedLesson::select('pre_r_lessons.genre_id', 'genres.title')
            ->where('instructor_id', Auth::user()->id)
            ->leftJoin("genres", 'genres.id', '=', "pre_r_lessons.genre_id")
            ->groupBy('pre_r_lessons.genre_id')
            ->get();


        return $this->sendResponse($instructorGenres);
    }

    public function update(CreatePreRLessonAPIRequest $request, $lesson)
    {
        if (!Auth::user()->canAddNewLesson()) {
            return $this->sendError('To add new lesson you must connect a submerchant account to your profile, upload profile image and have at least one media item in gallery', 400);
        }

        $lesson = $this->preRLessonRepository->findWithoutFail($lesson);

        if (empty($lesson)) {
            return $this->sendError('Lesson not found');
        }

        $user = Auth::user();

        $data = $request->toArray();


        if ($data['video'] && $data['video'] != $lesson->video) {
            $file_path = storage_path('app/public/videos/' . $user->id . '/' . $data['video']);

            $getID3 = new getID3;
            $file = $getID3->analyze($file_path);

            $data['duration'] = $file['playtime_string'];


            if (strlen($data['duration']) <= 5) {
                $data['duration'] = '00:' . $data['duration'];
            }

            $content = fopen($file_path, "r");

            $connectionString = "DefaultEndpointsProtocol=https;AccountName=skillective;AccountKey=nrKEkM7ihgQbuybiz/8NzsJJCdpwKQaxmmERc/F5x9kEXl/5RiXMet/5Mzw7QrA3jCdqUUd/9ETamRFatcheIg==;EndpointSuffix=core.windows.net";

            $contentType = "video/mp4";
            $options = new CreateBlockBlobOptions();
            $options->setContentType($contentType);

            try {

                $blobClient = BlobRestProxy::createBlobService($connectionString);
                $blobClient->createBlockBlob("public",  $data['video'], $content, $options);
                echo "success";
            } catch (ServiceException $e) {
                $this->sendError($e);
            }
        }

        $documents = $request->input('documents', null);

        $documentsCount = count($documents);

        $lessonFiles = $lesson->files;

        if ($lessonFiles) {
            foreach ($lessonFiles as $file) {
                $isExistInUpdateRequest = array_search($file->name, $documents);
                if (!$isExistInUpdateRequest) {
                    $file->delete();
                }
            }
        }

        if ($documentsCount > 0) {
            foreach ($documents as $key => $name) {

                $isFileAllreadyExist = PreRLessonFile::where('name', $name)->first();

                if (!$isFileAllreadyExist) {
                    PreRLessonFile::create(array('pre_r_lesson_id' => $lesson->id, 'name' => $name));
                }
            }
        }


        $data['instructor_id'] = $user->id;
        $data['genre_id'] = $data['genre'];
        unset($data['genre']);

        $lesson = $this->preRLessonRepository->update($data, $lesson['id']);

        return $this->sendResponse('Lesson updated');
    }

    public function remove($lesson)
    {

        if(!Auth::user()){
            return $this->sendError('Permission denied');
        }

        $user = Auth::user();

        $instructor_id = $user->id;

        $lesson = $this->preRLessonRepository->findWithoutFail($lesson);

        if (empty($lesson)) {
            return $this->sendError('Lesson not found');
        }

        if($lesson->instructor_id != $instructor_id){
            return $this->sendError('Permission denied');
        }

        $cancelled = $lesson->delete();

        if ($cancelled)
            return $this->sendResponse(true, 'Lesson removed successfully');
        else
            return $this->sendError('Can\'t remove the lesson', 400);
    }
}
