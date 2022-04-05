<?php

namespace App\Presenters;

use App\Transformers\BookingInListTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BookingInListPresenter.
 *
 * @package namespace App\Presenters;
 */
class BookingInListPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BookingInListTransformer();
    }
}
