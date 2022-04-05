<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class InstructorFilterByLocationCriteria implements CriteriaInterface
{
	protected $location;
	protected $city = null;
	protected $state = null;

	public function __construct($location)
	{
		$this->location = $location;
		$locationDetails = getLocationDetails($this->location);
		if (isset($locationDetails['city']))
			$this->city = $locationDetails['city'];
		if (isset($locationDetails['state']))
			$this->state = $locationDetails['state'];
	}

    public function apply($model, RepositoryInterface $repository)
    {
    	if ($this->city==null && $this->state==null) {
			$locationParts = explode(' ', $this->location);
			foreach ($locationParts as $part){
				$part = trim($part, ', ');
				$model = $model->whereRaw( "CONCAT(profiles.address, ', ', profiles.city, ', ', profiles.state) like '%" . escape_like($part) . "%'" );
			}
		}else{
            if ($this->city!=null)
                $model = $model->where('profiles.city', $this->city);
            // if ($this->state!=null) // commented by gerra
            //     $model = $model->where('profiles.state', $this->state);
		}

        return $model;
    }
}
