<?php

namespace App\Repositories;

use App\Models\Discount;

class DiscountRepository extends BaseRepository
{
    /**
     * @return string
     */
    public function model()
    {
        return Discount::class;
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
    public function getInstructorsDiscounts($id)
    {
        $discounts = $this->where('instructor_id', $id)->orderBy('created_at', 'desc')->get();
        return $discounts;
    }
}
