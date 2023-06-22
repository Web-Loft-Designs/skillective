<?php

namespace App\Repositories;

use App\Models\PreRecordedLesson;
use App\Criteria\PreRLessonFilterByContentCriteria;
use App\Criteria\PreRLessonFilterByGenresListCriteria;
use App\Criteria\PreRLessonFilterByInstructorNameCriteria;
use App\Criteria\PreRLessonFilterByGenreCriteria;
use App\Criteria\PreRLessonFilterByTopicCriteria;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Exceptions\RepositoryException;


class PreRLessonRepository extends BaseRepository
{

    protected $fieldSearchable = [
        'content',
    ];

    /**
     * @return string
     */
    public function model()
    {
        return PreRecordedLesson::class;
    }

    protected $skipPresenter = true;

    /**
     * @return string
     */
    public function presenter()
    {
        return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
    }

    /**
     * @param $data
     * @return mixed
     */
    public function presentResponse($data)
    {
        return $this->presenter->present($data);
    }


    /**
     * @param $request
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function getPreRLessons($request)
    {
        $this->resetCriteria();
        $this->resetScope();

        $this->pushCriteria(new RequestCriteria($request));
        $this->pushCriteria(new LimitOffsetCriteria($request));

        if ($request->filled('instructor_name')) {
            $this->pushCriteria(new PreRLessonFilterByInstructorNameCriteria($request->get('instructor_name')));
        }
        if ($request->filled('genre')) {
            $this->pushCriteria(new PreRLessonFilterByGenreCriteria($request->get('genre')));
        }
        if ($request->filled('topic')) {
            $this->pushCriteria(new PreRLessonFilterByTopicCriteria($request->get('topic')));
        }

        $this->scopeQuery(function ($query) use ($request) {
            $query = $query->join('users', 'pre_r_lessons.instructor_id', '=', 'users.id')
                ->leftJoin('user_genre', 'users.id', '=', 'user_genre.user_id')
                ->leftJoin('genres', 'pre_r_lessons.genre_id', '=', 'genres.id');

            if (Auth::check()) {
                $userId = Auth::user()->id;
                $query->select('pre_r_lessons.*', DB::raw('COUNT(purchased_lessons.pre_r_lesson_id) AS purchased_count'))
                    ->leftJoin('purchased_lessons', 'pre_r_lessons.id', '=', 'purchased_lessons.pre_r_lesson_id')
                    ->orderByRaw('CASE WHEN pre_r_lessons.genre_id IN (SELECT genre_id FROM user_genre WHERE user_id = ?) THEN 0 ELSE 1 END', [$userId])
                    ->groupBy('pre_r_lessons.id')
                    ->orderByDesc('purchased_count');

            } else {
                $query->select('pre_r_lessons.*', DB::raw('COUNT(purchased_lessons.pre_r_lesson_id) AS purchased_count'))
                    ->leftJoin('purchased_lessons', 'pre_r_lessons.id', '=', 'purchased_lessons.pre_r_lesson_id')
                    ->groupBy('pre_r_lessons.id')
                    ->orderByDesc('purchased_count');
            }

            return $query;
        });

        return $this->paginate(21, ['pre_r_lessons.*']);
    }

    /**
     * @param $request
     * @return LengthAwarePaginator|Collection|mixed
     * @throws RepositoryException
     */
    public function getInstructorPreRLessons($request)
    {
        $user = Auth::user();
        $instructor_id = $user->id;
        $this->resetCriteria();
        $this->resetScope();
        if ($request->filled('content'))
            $this->pushCriteria(new PreRLessonFilterByContentCriteria($request->get('content')));
        if ($request->filled('genres')) {
            $this->pushCriteria(new PreRLessonFilterByGenresListCriteria($request->get('genres')));
        }
        $this->scopeQuery(function ($query) use ($request, $instructor_id) {
            $query = $query->select('pre_r_lessons.*')
                ->addSelect(DB::raw('COUNT( DISTINCT purchased_lessons.id) as totalPurchares'))
                ->addSelect(DB::raw('COUNT( DISTINCT purchased_lessons.id) * purchased_lessons.price  as totalRevenue'))
                ->leftJoin('purchased_lessons', 'pre_r_lessons.id', '=', "purchased_lessons.pre_r_lesson_id")
                ->groupBy('pre_r_lessons.id')
                ->where('pre_r_lessons.instructor_id', $instructor_id)
                ->orderBy(DB::raw('COUNT(`purchased_lessons`.`id`)'), 'desc');
            return $query;
        })->with(['files']);

        if ($request->get('sortBy')) {
            if ($request->get('sortBy') === 'price_desc') {
                $this->orderBy('pre_r_lessons.price', 'desc');
            } else if ($request->get('sortBy') === 'price_asc') {
                $this->orderBy('pre_r_lessons.price', 'asc');
            } else if ($request->get('sortBy') === 'created_at_asc') {
                $this->orderBy('pre_r_lessons.created_at', 'asc');
            } else if ($request->get('sortBy') === 'created_at_desc') {
                $this->orderBy('pre_r_lessons.created_at', 'desc');
            }
        }

        return $this->paginate(21);
    }
}
