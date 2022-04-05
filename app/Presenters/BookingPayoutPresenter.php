<?php

namespace App\Presenters;

use App\Transformers\BookingPayoutTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BookingPayoutPresenter.
 *
 * @package namespace App\Presenters;
 */
class BookingPayoutPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BookingPayoutTransformer();
    }
}
