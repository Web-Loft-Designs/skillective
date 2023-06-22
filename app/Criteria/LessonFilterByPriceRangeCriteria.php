<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class LessonFilterByPriceRangeCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonFilterByPriceRangeCriteria implements CriteriaInterface
{
	protected $price_from;
	protected $price_to;

	public function __construct($price_from = null, $price_to = null)
	{
		$this->price_from	= $price_from;
		$this->price_to		= $price_to;
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
		$price_filter_field = 'lessons.spot_price';
		$this->price_from = is_numeric($this->price_from)?(float)$this->price_from : 0;
		$this->price_to = is_numeric($this->price_to)?(float)$this->price_to : 9999999;

		if ($this->price_from != null || $this->price_to != null) {
			if ($this->price_from != null && $this->price_to != null) {
				if ($this->price_from < $this->price_to) {
					$model = $model->whereRaw(" $price_filter_field >= ".$this->price_from." AND $price_filter_field <= ".$this->price_to);
				} else {
					$model = $model->whereRaw(" $price_filter_field = ".$this->price_from);
				}
			} elseif ($this->price_from !== null) {
				$model = $model->whereRaw(" $price_filter_field >= ".$this->price_from);
			} elseif ($this->price_to !== null) {
				$model = $model->whereRaw(" $price_filter_field <= ".$this->price_to);
			}
		}
        return $model;
    }
}
