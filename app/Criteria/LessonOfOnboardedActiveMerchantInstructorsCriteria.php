<?php

namespace App\Criteria;

use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class LessonOfOnboardedActiveMerchantInstructorsCriteria.
 *
 * @package namespace App\Criteria;
 */
class LessonOfOnboardedActiveMerchantInstructorsCriteria implements CriteriaInterface
{
	public function __construct() {}

    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
		$model = $model->whereNotNull('users.bt_submerchant_id')
					   ->where('users.bt_submerchant_status', \Braintree_MerchantAccount::STATUS_ACTIVE);
        return $model;
    }
}
