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
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $model = $model->whereRaw("CONCAT(title, ' ', description) LIKE '%".escape_like($this->topic)."%'");

        return $model;
    }
}
