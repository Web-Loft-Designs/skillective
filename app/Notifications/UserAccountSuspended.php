<?php

namespace App\Notifications;

use App\Models\CustomNotification;
use App\Models\User;

class UserAccountSuspended extends AbstractCustomNotification
{
    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('user_account_suspended')->first();
    }
}