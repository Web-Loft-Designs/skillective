<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User;

/**
 * Class UsersInListTransformer.
 *
 * @package namespace App\Transformers;
 */
class UsersInListTransformer extends TransformerAbstract
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
		return [
			'id' => (int)$model->id,
			'first_name' => $model->first_name,
			'last_name' => $model->last_name,
			'full_name' => $model->getName(),
			'email' => $model->getEmail(),
			'status' => $model->status,
			'profile' => [
				'id' =>  $model->profile->id,
				'user_id' => $model->profile->user_id,
				'instagram_handle' => $model->profile->instagram_handle,
				'address' => $model->profile->address,
				'city' => $model->profile->city,
				'state' => $model->profile->state,
				'zip' => $model->profile->zip,
				'full_address' => $model->profile->getFullAddress(),
				'mobile_phone' => presentMobilePhone($model->profile->mobile_phone),
				'dob' => $model->profile->dob ? $model->profile->dob->toDateString() : null,
				'about_me' => $model->profile->about_me,
				'image' => $model->profile->getImageUrl(),
				'notification_methods' => $model->profile->notification_methods,
				//				'instagram_followers_count' => $model->profile->instagram_followers_count,
				'gender' => $model->profile->gender,
			],
			'priority' => $model->isFeatured ?  $model->isFeatured->priority : 0,
			'isFeatured' => $model->isFeatured ? true : false
		];
	}
}
