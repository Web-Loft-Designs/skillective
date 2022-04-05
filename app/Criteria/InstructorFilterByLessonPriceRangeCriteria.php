<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Log;

class InstructorFilterByLessonPriceRangeCriteria implements CriteriaInterface
{
	protected $rate_from;
	protected $rate_to;

	public function __construct($rate_from = null, $rate_to = null)
	{
		$this->rate_from	= $rate_from;
		$this->rate_to		= $rate_to;
	}

    public function apply($model, RepositoryInterface $repository)
    {
//		$rate_filter_field = 'bookings.spot_price';
		$this->rate_from = is_numeric($this->rate_from)?(float)$this->rate_from : 0;
		$this->rate_to = is_numeric($this->rate_to)?(float)$this->rate_to : 9999999;

		if ($this->rate_from != null || $this->rate_to != null) {
			if ($this->rate_from != null && $this->rate_to != null) {
				if ($this->rate_from < $this->rate_to) {
					$model = $model->whereRaw(" min_rate >= ".$this->rate_from." AND max_rate <= ".$this->rate_to);
				} else {
					$model = $model->whereRaw(" min_rate = ".$this->rate_from);
				}
			} elseif ($this->rate_from !== null) {
				$model = $model->whereRaw(" min_rate >= ".$this->rate_from);
			} elseif ($this->rate_to !== null) {
				$model = $model->whereRaw(" max_rate <= ".$this->rate_to);
			}
		}
        return $model;
    }
}
