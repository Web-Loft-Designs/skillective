<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\API\UpdateInstructorGoalAPIRequest;
use App\Http\Requests\API\DeleteInstructorGoalAPIRequest;


class InstructorGoalAPIController extends AppBaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request) {
		return $this->sendResponse($request->user()->profile->getGoal());
	}

    /**
     * @param UpdateInstructorGoalAPIRequest $request
     * @return JsonResponse
     */
    public function update(UpdateInstructorGoalAPIRequest $request) {
		$currentUser = $request->user();

		$currentUser->profile->goal_value = $request->input('goal_value', null);
		$currentUser->profile->goal_description = $request->input('goal_description', null);
		$currentUser->profile->goal_color = $request->input('goal_color', null);
		$currentUser->profile->save();

		return $this->sendResponse(true, 'Goal updated');
	}

    /**
     * @param DeleteInstructorGoalAPIRequest $request
     * @return JsonResponse
     */
    public function delete(DeleteInstructorGoalAPIRequest $request) {
		$currentUser = $request->user();

		$currentUser->profile->goal_value = null;
		$currentUser->profile->goal_description = null;
		$currentUser->profile->goal_color = null;
		$currentUser->profile->save();

		return $this->sendResponse(true, 'Goal deleted.');
	}
}
