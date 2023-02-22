<?php

namespace App\Transformers;


use App\Models\User;

/**
 * Class InstructorsInListTransformer.
 *
 * @package namespace App\Transformers;
 */
class InstructorsInListTransformer extends UsersInListTransformer
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
		$transformation['genres'] = $model->genres->toArray();

		return $transformation;
    }
}
