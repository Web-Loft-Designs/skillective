<?php

namespace App\Criteria;

use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Http\Request;

/**
 * Class LessonScheduleForWeekCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonScheduleForWeekCriteria implements CriteriaInterface
{
	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
	}


    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
		if ($this->request->has('week') && (Carbon::createFromFormat('Y-m-d', $this->request->get('week'))) ) {
			$weekStartDay = date("Y-m-d", strtotime('monday this week', strtotime($this->request->get('week'))));
            $weekEndDay = date("Y-m-d", strtotime('sunday next week', strtotime($this->request->get('week'))));
            
            
			$model = $model->whereRaw( " ( DATE(lessons.start) BETWEEN '" . $weekStartDay . "' AND '" . $weekEndDay . "' OR DATE(lessons.end) BETWEEN '" . $weekStartDay . "' AND '" . $weekEndDay . "' ) ");
		}
        return $model;
    }
}
