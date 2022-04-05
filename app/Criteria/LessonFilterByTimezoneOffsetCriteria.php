<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class LessonFilterByTimezoneOffsetCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonFilterByTimezoneOffsetCriteria implements CriteriaInterface
{
	protected $timezone_offset_gmt;

	public function __construct($timezone_offset_gmt)
	{
		$this->timezone_offset_gmt = $timezone_offset_gmt;
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
		$model = $model->where("lessons.timezone_offset_gmt", $this->timezone_offset_gmt);
        return $model;
    }
}
