<?php

namespace App\Presenters;

use App\Transformers\PurchasedLessonSingleTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class LessonInListPresenter.
 *
 * @package namespace App\Presenters;
 */
class PurchasedLessonSinglePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new PurchasedLessonSingleTransformer();
    }
}
