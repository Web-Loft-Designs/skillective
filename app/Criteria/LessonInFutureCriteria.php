<?php

namespace App\Criteria;

use App\Models\Lesson;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Carbon\Carbon;

/**
 * Class LessonInFutureCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonInFutureCriteria implements CriteriaInterface
{
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
		$model->whereRaw("CONVERT_TZ('$nowOnServer', 'GMT', lessons.timezone_id) <= lessons.start")
			->whereRaw(" ( lessons.is_cancelled is NULL OR lessons.is_cancelled=0 ) ");
        return $model;
    }
}
