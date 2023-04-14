<?php

use Illuminate\Database\Seeder;

class NotificationSmsMessageSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Throwable
     */
    public function run(\App\Models\CustomNotificationMethod $method)
    {

        $message = 'Message from: [[sender_name]] via Skillective. [[message]] Login to <a href="[[app_url]]">Skillective.com</a> to communicate with your instructor.';
        $method->where('id', 34)->update(['content' => $message]);

    }
}
