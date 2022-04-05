<?php

namespace App\Criteria;

use Carbon\Carbon;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Illuminate\Http\Request;

/**
 * Class LessonScheduleForDayCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonScheduleForDayCriteria implements CriteriaInterface
{

	protected $request;

	public function __construct(Request $request)
	{
		$this->request = $request;
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
		if ($this->request->has('day') && ($carbon = \Carbon\Carbon::createFromFormat('Y-m-d', $this->request->get('day'))) )
			$model = $model->whereRaw("( DATE(lessons.start) = '" . $this->request->get('day') . "' OR DATE(lessons.end) = '" . $this->request->get('day') . "' OR '" . $this->request->get('day') . "' BETWEEN DATE(lessons.start) AND DATE(lessons.end) )");
		return $model;
    }
}
