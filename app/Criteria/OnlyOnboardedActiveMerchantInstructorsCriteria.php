<?php

namespace App\Criteria;

use Braintree\MerchantAccount;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

class OnlyOnboardedActiveMerchantInstructorsCriteria implements CriteriaInterface
{
	public function __construct() {}

    public function apply($model, RepositoryInterface $repository)
    {
		$model = $model->whereNotNull('users.bt_submerchant_id')
					   ->where('users.bt_submerchant_status', MerchantAccount::STATUS_ACTIVE);
        return $model;
    }
}
