<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Response;
use Auth;
use App\Http\Requests\API\UpdateInstructorGoalAPIRequest;
use App\Http\Requests\API\DeleteInstructorGoalAPIRequest;
use App\Models\Profile;
use App\Models\User;
use Log;

class InstructorGoalAPIController extends AppBaseController
{
	public function get(Request $request) {
		return $this->sendResponse($request->user()->profile->getGoal());
	}

	public function update(UpdateInstructorGoalAPIRequest $request) {
		$currentUser = $request->user();

		$currentUser->profile->goal_value = $request->input('goal_value', null);
		$currentUser->profile->goal_description = $request->input('goal_description', null);
		$currentUser->profile->goal_color = $request->input('goal_color', null);
		$currentUser->profile->save();

		return $this->sendResponse(true, 'Goal updated');
	}

	public function delete(DeleteInstructorGoalAPIRequest $request) {
		$currentUser = $request->user();

		$currentUser->profile->goal_value = null;
		$currentUser->profile->goal_description = null;
		$currentUser->profile->goal_color = null;
		$currentUser->profile->save();

		return $this->sendResponse(true, 'Goal deleted.');
	}
}
