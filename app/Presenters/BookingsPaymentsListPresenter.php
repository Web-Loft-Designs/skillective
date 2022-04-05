<?php

namespace App\Presenters;

use App\Transformers\BookingsPaymentsListTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BookingsPaymentsListPresenter.
 *
 * @package namespace App\Presenters;
 */
class BookingsPaymentsListPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BookingsPaymentsListTransformer();
    }
}
