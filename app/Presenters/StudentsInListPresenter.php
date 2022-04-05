<?php

namespace App\Presenters;

use App\Transformers\StudentsInListTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class StudentsInListPresenter.
 *
 * @package namespace App\Presenters;
 */
class StudentsInListPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new StudentsInListTransformer();
    }
}
