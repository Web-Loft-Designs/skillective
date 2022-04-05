<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use Log;

/**
 * Class LessonFilterByGenreCriteria.
 *
 * @package namespace App\Criteria;
 */
class PreRLessonFilterByGenresListCriteria implements CriteriaInterface
{
    protected $genre_id;

    public function __construct($genres)
    {
        $this->genres = $genres;
    }

    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {

        $array = array_map('intval', explode(',', $this->genres));

        Log::info($array);

        $model = $model->whereIn("genre_id", $array);
        return $model;
    }
}
