<?php

namespace App\Transformers;

use App\Transformers\UsersInListTransformer;
use League\Fractal\TransformerAbstract;
use App\Models\User;

/**
 * Class StudentsInListTransformer.
 *
 * @package namespace App\Transformers;
 */
class StudentsInListTransformer extends UsersInListTransformer
{
    /**
     * Transform the User entity.
     *
     * @param \App\Models\User $model
     *
     * @return array
     */
    public function transform(User $model)
    {
		$transformation = parent::transform($model);



		return $transformation;
    }
}
