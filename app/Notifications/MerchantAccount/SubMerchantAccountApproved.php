<?php

namespace App\Notifications\MerchantAccount;

use App\Models\CustomNotification;
use App\Models\User;
use App\Notifications\AbstractCustomNotification;

class SubMerchantAccountApproved extends AbstractCustomNotification
{
    /**
     * @var \App\Models\User
     */
	private $user;

	public function __construct(User $user)
	{
		$this->user = $user;

		parent::__construct();
	}

    public function variables()
    {
        return [
			
        ];
    }

    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('sub_merchant_account_approved')->first();
    }
}