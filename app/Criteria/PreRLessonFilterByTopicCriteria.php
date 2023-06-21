<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class LessonFilterByGenreCriteria.
 *
 * @package namespace App\Criteria;
 */
class PreRLessonFilterByTopicCriteria implements CriteriaInterface
{
	protected $topic;

	public function __construct($topic)
	{
		$this->topic = $topic;
	}


    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $model = $model->whereRaw("CONCAT(pre_r_lessons.title, ' ', pre_r_lessons.description) LIKE '%".escape_like($this->topic)."%'");
        return $model;
    }
}
