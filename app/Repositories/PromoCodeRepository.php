<?php

namespace App\Repositories;

use App\Models\PromoCode;
use InfyOm\Generator\Common\BaseRepository;
use Auth;

class PromoCodeRepository extends BaseRepository
{
    public function model()
    {
        return PromoCode::class;
    }

    protected $skipPresenter = true;

    public function presenter()
    {
        return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
    }

    public function getInstrucorsPromos()
	{
		$instructor_id = Auth::user()->id;

		$this->resetCriteria();
		$this->resetScope();

		$this->scopeQuery(function ($query) use ($instructor_id) {
			$query->where('instructor_id', $instructor_id)
				->orderBy('created_at', 'desc');
			return $query;
		});


		return $this->get(['promo_codes.*']);
	}


    public function presentResponse($data)
    {
        return $this->presenter->present($data);
    }

    public function getInstructorPromos($id)
    {

        $this->resetCriteria();
        $this->resetScope();

        $this->scopeQuery(function ($query) use ($id) {
            $query->where('instructor_id', $id)
                ->orderBy('created_at', 'desc');
            return $query;
        });


        return $this->get(['promo_codes.*']);
    }

}
