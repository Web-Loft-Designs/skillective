<?php

namespace App\Presenters;

use App\Transformers\InstructorsInSearchListTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

class InstructorsInSearchListPresenter extends FractalPresenter
{
    public function getTransformer()
    {
        return new InstructorsInSearchListTransformer();
    }
}
