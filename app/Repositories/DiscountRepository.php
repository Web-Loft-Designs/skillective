<?php

namespace App\Repositories;

use App\Models\Discount;

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



    public function presentResponse($data)
    {
        return $this->presenter->present($data);
    }

    public function getInstructorsDiscounts($id)
    {

        $discounts = $this->where('instructor_id', $id)->orderBy('created_at', 'desc')->get();

        return $discounts;

    }
}
