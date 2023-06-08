<?php

namespace App\Repositories;

use App\Models\PromoCode;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PromoCodeRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return PromoCode::class;
    }

    protected $skipPresenter = true;

    /**
     * @return string
     */
    public function presenter()
    {
        return "Prettus\\Repository\\Presenter\\ModelFractalPresenter";
    }

    /**
     * @return LengthAwarePaginator|Collection|mixed
     */
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


    /**
     * @param $data
     * @return mixed
     */
    public function presentResponse($data)
    {
        return $this->presenter->present($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getInstructorPromos($id)
    {
        $promos = $this->where('instructor_id', $id)->orderBy('created_at', 'desc')->get();
        return $promos;
    }

}
