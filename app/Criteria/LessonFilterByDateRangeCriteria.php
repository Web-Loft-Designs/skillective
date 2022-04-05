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
class LessonFilterByDateRangeCriteria implements CriteriaInterface
{
	protected $date_from;
	protected $date_to;

	public function __construct($date_from = null, $date_to = null)
	{
		$this->date_from	= $date_from;
		$this->date_to		= $date_to;
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
			$this->date_from = ($this->date_from!=null && preg_match('/\d{4}\/\d{1,2}\/\d{1,2}/', $this->date_from)) ? \Carbon\Carbon::parse($this->date_from)->format('Y-m-d') : null;
		}catch (\Exception $e){
			$this->date_from = null;
			Log::error('date_from:' . $e->getMessage());
		}
		try{
			$this->date_to = ($this->date_to!=null && preg_match('/\d{4}\/\d{1,2}\/\d{1,2}/', $this->date_to)) ? \Carbon\Carbon::parse($this->date_to)->format('Y-m-d') : null;
		}catch (\Exception $e){
			$this->date_to = null;
			Log::error('date_from:' . $e->getMessage());
		}

		if ($this->date_from != null || $this->date_to != null) {
			if ($this->date_from != null && $this->date_to != null) {
				if ($this->date_from != $this->date_to) {
					$model = $model->whereRaw(" DATE($date_filter_field) BETWEEN '".$this->date_from."' AND '".$this->date_to."'");
				} else {
					$model = $model->whereRaw(" DATE($date_filter_field) = '".$this->date_from."'");
				}
			} elseif ($this->date_from != null) {
				$model = $model->whereRaw(" DATE($date_filter_field) >= '".$this->date_from."'");
			} elseif ($this->date_to != null) {
				$model = $model->whereRaw(" DATE($date_filter_field) <= '".$this->date_to."'");
			}
		}
        return $model;
    }
}
