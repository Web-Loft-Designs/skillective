<?php

namespace App\Presenters;

use App\Transformers\PreRLessonInListTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class LessonInListPresenter.
 *
 * @package namespace App\Presenters;
 */
class PreRLessonInListPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new PreRLessonInListTransformer();
    }
}
