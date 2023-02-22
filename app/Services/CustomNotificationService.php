<?php

namespace App\Services;


use App\Models\CustomNotification;
use App\Models\CustomNotificationMethod;

class CustomNotificationService
{
    /**
     * @param CustomNotification $notification
     * @param array $data
     * @return void
     */
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