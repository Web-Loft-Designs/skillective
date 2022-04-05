<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class LessonFilterByGenreCriteria.
 *
 * @package namespace App\Criteria;
 */
class PreRLessonFilterByContentCriteria implements CriteriaInterface
{
	protected $content;

	public function __construct($content)
	{
		$this->content = $content;
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
        if($this->content == 'with_video'){
            $model = $model->has('files', '<', 1)->where("video", "<>", '');
        }
        else if($this->content == 'with_documents'){
            $model->whereNull('video')->has('files');
        }

        return $model;
    }
}
