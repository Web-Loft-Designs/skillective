<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\User;

/**
 * Class StudentInstructorInListTransformer.
 *
 * @package namespace App\Transformers;
 */
class StudentInstructorInListTransformer extends TransformerAbstract
{
    /**
     * Transform the Lesson entity.
     *
     * @param \App\Models\Lesson $model
     *
     * @return array
     */
    public function transform(User $model)
    {
        $transformation = [
			'id' => $model->id,
			'first_name' => $model->first_name,
			'last_name'=> $model->last_name,
			'full_name'=> $model->getName(),
//			'email'=> $model->getEmail(),
			'profile' => $model->profile->transform(),
			'genres' => $model->genres->toArray(),
			'isFavorite' => $model->is_favorite, // pivot table attr
			'geoNotificationsAllowed' => $model->geo_notifications_allowed, // pivot table attr
			'virtualNotificationsAllowed' => $model->virtual_notifications_allowed // pivot table attr
        ];

        unset($transformation['profile']['about_me']);
        unset($transformation['profile']['notification_methods']);
        unset($transformation['profile']['dob']);
        unset($transformation['profile']['mobile_phone']);
        unset($transformation['profile']['address']);
        unset($transformation['profile']['city']);
        unset($transformation['profile']['state']);
        unset($transformation['profile']['zip']);
        unset($transformation['profile']['full_address']);
        unset($transformation['profile']['max_allowed_instructor_invites']);
        unset($transformation['profile']['lat']);
        unset($transformation['profile']['lng']);

        return $transformation;
    }
}
