<?php

namespace App\Notifications;

use App\CustomNotification;

class PasswordResetNotification extends AbstractCustomNotification
{
    private $reset_url;

    private $token;

    private $email;

    public function __construct($reset_url, $token, $email)
    {
        $this->reset_url = $reset_url;
        $this->token = $token;
        $this->email = $email;

        parent::__construct();
    }

    public function variables()
    {
        return [
            'reset_url' =>  trim($this->reset_url, '/') . '/' . $this->token . '?email=' . $this->email
        ];
    }

    /**
     * @return \App\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('reset_password')->first();
    }
}