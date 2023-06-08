<?php

namespace App\Criteria;

use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Http\Request;

/**
 * Class LessonScheduleForMonthCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonScheduleForMonthCriteria implements CriteriaInterface
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
		if ($this->request->has('month') && ($carbon = Carbon::createFromFormat('Y-m-d', $this->request->get('month'))) )
			$model = $model->whereRaw("MONTH(lessons.start) = '" . $carbon->format('m') . "'");
        return $model;
    }
}
