<?php

namespace App\Presenters;

use App\Transformers\PreRLessonSingleTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class LessonInListPresenter.
 *
 * @package namespace App\Presenters;
 */
class PreRLessonSinglePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new PreRLessonSingleTransformer();
    }
}
