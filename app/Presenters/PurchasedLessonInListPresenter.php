<?php

namespace App\Presenters;

use App\Transformers\PurchasedLessonInListTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class LessonInListPresenter.
 *
 * @package namespace App\Presenters;
 */
class PurchasedLessonInListPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new PurchasedLessonInListTransformer();
    }
}
