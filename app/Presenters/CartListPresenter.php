<?php

namespace App\Presenters;

use App\Transformers\CartListTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BookingInListPresenter.
 *
 * @package namespace App\Presenters;
 */
class CartListPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new CartListTransformer();
    }
}
