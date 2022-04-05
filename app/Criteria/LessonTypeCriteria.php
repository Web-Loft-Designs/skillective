<?php

namespace App\Criteria;

use App\Models\Lesson;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Carbon\Carbon;

/**
 * Class LessonFilterByInstructorNameCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonTypeCriteria implements CriteriaInterface
{
	protected $lessonType;

	public function __construct($lessonType)
	{
		$this->lessonType = $lessonType;
	}

    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
		$nowOnServer = Carbon::now()->format('Y-m-d H:i:s'); // UTC
		switch ($this->lessonType){
			case 'past':
				$model->orderBy('lessons.start','desc')
					  ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
					  ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) >= lessons.start");
				break;
			case 'cancelled':
				$model = $model->where("lessons.is_cancelled", 1);
				break;
			case 'current':
			default:
			$model = $model->orderBy('lessons.start','asc')
						   ->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ")
						   ->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) < lessons.start");
				break;
		}

        return $model;
    }
}
