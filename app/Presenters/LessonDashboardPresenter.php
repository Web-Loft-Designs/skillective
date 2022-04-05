<?php

namespace App\Presenters;

use App\Transformers\LessonDashboardTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class BookingInListPresenter.
 *
 * @package namespace App\Presenters;
 */
class LessonDashboardPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new LessonDashboardTransformer();
    }
}
