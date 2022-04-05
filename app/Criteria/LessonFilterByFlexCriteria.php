<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Log;

/**
 * Class LessonFilterByDateRangeCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonFilterByFlexCriteria implements CriteriaInterface
{
	protected $flexible_days;
	protected $flexible_months;

	public function __construct($flexible_months = null, $flexible_days = null)
	{
		$this->flexible_days	= $flexible_days;
		$this->flexible_months		= $flexible_months;
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
		$date_filter_field = 'lessons.start';

		$model = $model->whereRaw(" MONTH($date_filter_field) IN (" . $this->flexible_months . ")");

        if($this->flexible_days){
            if($this->flexible_days == "During the Week"){
                $model = $model->whereRaw(" DAYNAME($date_filter_field) NOT IN ('Saturday', 'Sunday') ");
            }
            else if($this->flexible_days == "Weekends"){
                $model = $model->whereRaw(" DAYNAME($date_filter_field) IN ('Saturday', 'Sunday') ");
            }
           
        }


        
        return $model;
    }
}
