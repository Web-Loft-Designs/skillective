<?php

namespace App\Transformers;

use App\Transformers\UsersInListTransformer;
use League\Fractal\TransformerAbstract;
use App\Models\User;

class InstructorsInSearchListTransformer extends UsersInListTransformer
{
    public function transform(User $model)
    {
		$transformation = parent::transform($model);
		$transformation['profile']['lat'] = $model->profile->lat;
		$transformation['profile']['lng'] = $model->profile->lng;
        $transformation['min_rate'] = $model->min_rate;
        $transformation['max_rate'] = $model->max_rate;
        unset($transformation['email']);
		unset($transformation['profile']['about_me']);
		unset($transformation['profile']['notification_methods']);
		unset($transformation['profile']['dob']);
		unset($transformation['profile']['mobile_phone']);
		unset($transformation['profile']['gender']);
		unset($transformation['profile']['address']);
		unset($transformation['profile']['zip']);
		unset($transformation['profile']['full_address']);

		return $transformation;
    }
}
