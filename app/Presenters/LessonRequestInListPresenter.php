<?php

namespace App\Presenters;

use App\Transformers\LessonRequestInListTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

class LessonRequestInListPresenter extends FractalPresenter
{
    public function getTransformer()
    {
        return new LessonRequestInListTransformer();
    }
}
