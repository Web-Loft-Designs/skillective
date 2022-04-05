<?php

namespace App\Presenters;

use App\Transformers\LessonInListTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class LessonInListPresenter.
 *
 * @package namespace App\Presenters;
 */
class LessonInListPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new LessonInListTransformer();
    }
}
