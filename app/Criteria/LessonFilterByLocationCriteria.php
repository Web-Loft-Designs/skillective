<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class LessonFilterByLocationCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonFilterByLocationCriteria implements CriteriaInterface
{
	protected $location;
	protected $city = null;
	protected $state = null;

	public function __construct($location)
	{
		$this->location = $location;
//		$locationDetails = getLocationDetails($this->location);
//		if (isset($locationDetails['city']))
//			$this->city = $locationDetails['city'];
//		if (isset($locationDetails['state']))
//			$this->state = $locationDetails['state'];
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
    	if ($this->city==null && $this->state==null) {
			$locationParts = explode(' ', $this->location);
			foreach ($locationParts as $part){
				$part = trim($part, ', ');
				$model = $model->where( "lessons.location", 'like', '%' . escape_like($part) . '%' );
			}
		}else{
            if ($this->city!=null)
                $model = $model->where('lessons.city', $this->city);
            if ($this->state!=null)
                $model = $model->where('lessons.state', $this->state);
		}

        return $model;
    }
}
