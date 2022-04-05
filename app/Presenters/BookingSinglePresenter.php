<?php

namespace App\Presenters;

use App\Transformers\BookingSingleTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class LessonSinglePresenter.
 *
 * @package namespace App\Presenters;
 */
class BookingSinglePresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new BookingSingleTransformer();
    }
}
