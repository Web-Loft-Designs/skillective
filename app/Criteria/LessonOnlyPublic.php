<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Auth;

class LessonOnlyPublic implements CriteriaInterface
{
    public function apply($model, RepositoryInterface $repository)
    {
        if (Auth::id())
		    $model	= $model->whereRaw('lessons.private_for_student_id IS NULL OR lessons.private_for_student_id='.Auth::id());
        else
            $model	= $model->whereRaw('lessons.private_for_student_id IS NULL');
        return $model;
    }
}
