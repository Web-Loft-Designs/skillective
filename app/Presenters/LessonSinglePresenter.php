<?php

namespace App\Presenters;

use App\Transformers\LessonSingleTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class LessonSinglePresenter.
 *
 * @package namespace App\Presenters;
 */
class LessonSinglePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new LessonSingleTransformer();
    }
}
