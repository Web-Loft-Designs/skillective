<?php

namespace App\Presenters;

use App\Transformers\UsersInListTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class UsersInListPresenter.
 *
 * @package namespace App\Presenters;
 */
class UsersInListPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new UsersInListTransformer();
    }
}
