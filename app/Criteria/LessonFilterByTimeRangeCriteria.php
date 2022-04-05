<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Log;

/**
 * Class LessonFilterByTimeRangeCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonFilterByTimeRangeCriteria implements CriteriaInterface
{
	protected $time_from;
	protected $time_to;

	public function __construct($time_from = null, $time_to = null)
	{
		$this->time_from	= $time_from;
		$this->time_to		= $time_to;
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
		try{
			$this->time_from = ($this->time_from!=null && preg_match('/\d{1,2}\:\d{2}(\:\d{2})?/', $this->time_from)) ? \Carbon\Carbon::createFromTimeString($this->time_from)->format('H:i:00') : null;
		}catch (\Exception $e){
			$this->time_from = null;
			Log::error('time_from: ' . $e->getMessage());
		}
		try{
			$this->time_to = ($this->time_to!=null && preg_match('/\d{1,2}\:\d{2}(\:\d{2})?/', $this->time_to)) ? \Carbon\Carbon::createFromTimeString($this->time_to)->format('H:i:00') : null;
		}catch (\Exception $e){
			$this->time_to = null;
			Log::error('time_to:' . $e->getMessage());
		}

		if ($this->time_from != null || $this->time_to != null) {
			if ($this->time_from != null && $this->time_to != null) {
				if ($this->time_from != $this->time_to) {
					$model = $model->whereRaw(" TIME($date_filter_field) BETWEEN '".$this->time_from."' AND '".$this->time_to."'");
				} else {
					$model = $model->whereRaw(" TIME($date_filter_field) = '".$this->time_from."'");
				}
			} elseif ($this->time_from != null) {
				$model = $model->whereRaw(" TIME($date_filter_field) >= '".$this->time_from."'");
			} elseif ($this->time_to != null) {
				$model = $model->whereRaw(" TIME($date_filter_field) <= '".$this->time_to."'");
			}
		}
        return $model;
    }
}
