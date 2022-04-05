<?php

namespace App\Presenters;

use App\Transformers\InstructorsInListTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class InstructorsInListPresenter.
 *
 * @package namespace App\Presenters;
 */
class InstructorsInListPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new InstructorsInListTransformer();
    }
}
