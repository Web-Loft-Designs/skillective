<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class LessonFilterByInstagranHandleCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonFilterByInstagranHandleCriteria implements CriteriaInterface
{
	protected $instagram_handle;

	public function __construct($instagram_handle)
	{
		$this->instagram_handle = $instagram_handle;
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
		$model = $model->where("profiles.instagram_handle", 'like', '%' . ltrim($this->instagram_handle, '@') . '%');
        return $model;
    }
}
