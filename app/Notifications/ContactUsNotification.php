<?php

namespace App\Notifications;

use App\Models\CustomNotification;

class ContactUsNotification extends AbstractCustomNotification
{
    private $formData;

    public function __construct($formData)
    {
        $this->formData = $formData;

        parent::__construct();
    }

    public function variables()
    {
        return [
            'first_name' => $this->formData['first_name'],
            'last_name' => $this->formData['last_name'],
            'address' => $this->formData['address'],
			'mobile_phone' => $this->formData['mobile_phone'],
			'email' => $this->formData['email'],
			'reason' => $this->formData['reason'],
        ];
    }

    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('contact_us_form_request')->first();
    }
}