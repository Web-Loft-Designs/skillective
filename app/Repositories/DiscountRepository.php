<?php

namespace App\Repositories;

use App\Models\Discount;
use InfyOm\Generator\Common\BaseRepository;
use Auth;

class DiscountRepository extends BaseRepository
{
    public function model()
    {
        return Discount::class;
    }

    protected $skipPresenter = true;

    public function presenter()
    {
        return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
    }



	public function getInstructorsDiscounts()
	{
		$instructor_id = Auth::user()->id;

		$this->resetCriteria();
		$this->resetScope();


		$this->scopeQuery(function ($query) use ($instructor_id) {
			$query->where('instructor_id', $instructor_id)
				->orderBy('created_at', 'desc');
			return $query;
		});


		return $this->get(['discounts.*']);
	}


    public function presentResponse($data)
    {
        return $this->presenter->present($data);
    }
}
