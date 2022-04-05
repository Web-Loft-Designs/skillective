<?php
/**
 * Created by PhpStorm.
 * User: oleghalin
 * Date: 16/11/2018
 * Time: 10:36
 */

namespace App\Services;

use App\CustomNotification;
use App\CustomNotificationMethod;
use Illuminate\Notifications\Notification;

class CustomNotificationService
{
    public function update(CustomNotification $notification, array $data)
    {
        if ($data['sms']['checked']) {
            CustomNotificationMethod::updateOrCreate([
                'method'                 => 'sms',
                'custom_notification_id' => $notification->getKey(),
            ], [
                'content' => $data['sms']['content'],
            ]);
        } else {
            $notification->methods()->where('method', 'sms')->update([
                'active' => false,
            ]);
        }

        if ($data['mail']['checked']) {
            CustomNotificationMethod::updateOrCreate([
                'method' => 'mail',

                'custom_notification_id' => $notification->getKey(),
            ], [
                'content' => $data['mail']['content'],
                'data'    => [
                    'subject' => $data['mail']['subject'],
                ],
            ]);
        } else {
            $notification->methods()->where('method', 'mail')->update([
                'active' => false,
            ]);
        }

		if ($data['whatsapp']['checked']) {
			CustomNotificationMethod::updateOrCreate([
				'method' => 'whatsapp',

				'custom_notification_id' => $notification->getKey(),
			], [
				'content' => $data['whatsapp']['content'],
				'data'    => [
					'subject' => $data['whatsapp']['subject'],
				],
			]);
		} else {
			$notification->methods()->where('method', 'whatsapp')->update([
				'active' => false,
			]);
		}
    }
}