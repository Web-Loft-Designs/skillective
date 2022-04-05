<?php

namespace App\Notifications\MerchantAccount;

use App\Models\CustomNotification;
use App\Models\User;
use App\Notifications\AbstractCustomNotification;

class SubMerchantAccountDeclined extends AbstractCustomNotification
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
			'merchant_data_edit_url' => route('profile.edit') . '#merchant-account-trigger',
			'reason' => $this->user->bt_submerchant_status_reason
        ];
    }

    /**
     * @return \App\Models\CustomNotification
     */
    protected function getCustomNotificationClass(): CustomNotification
    {
        return CustomNotification::query()->whereTag('sub_merchant_account_declined')->first();
    }
}