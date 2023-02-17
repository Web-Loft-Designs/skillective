<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Requests\API\UpdateUserGeoLocationsAPIRequest;
use App\Http\Requests\API\DeleteUserGeoLocationsAPIRequest;
use App\Models\UserGeoLocation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UserGeoLocationAPIController extends AppBaseController
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
	{
		return $request->user()->geoLocations()->get();
	}

	public function update(UpdateUserGeoLocationsAPIRequest $request, User $user = null) {

		if (!Auth::user()->hasRole(User::ROLE_ADMIN)){
			$user = Auth::user();
		}

		$currentUser = $request->user();
			$geoLocations = $request->all();
			foreach ($geoLocations as $index=>$geoLocation){
				$id = $geoLocation['id'];
				if ($id){
					$toUpdateUserGeoLocation = $currentUser->geoLocations()->find($id);
					if ($toUpdateUserGeoLocation && $toUpdateUserGeoLocation->user_id != $currentUser->id)
						$this->sendError('Invalid Request', 403);
				}
				$input = [
					'location' => $geoLocation['location'],
					'limit' => $geoLocation['limit'],
					'date_from' => $geoLocation['date_from'],
					'date_to' => $geoLocation['date_to'],
				];
				$currentUser->geoLocations()->updateOrCreate([
					'id' => $id
				], $input);
			}

		return $this->sendResponse($currentUser->geoLocations()->get()->toArray(), 'Geo limitations updated');
	}

    /**
     * @param DeleteUserGeoLocationsAPIRequest $request
     * @param UserGeoLocation $geolocation
     * @return JsonResponse
     */
    public function destroy(DeleteUserGeoLocationsAPIRequest $request, UserGeoLocation $geolocation) {
		if (  !Auth::user()->hasRole(User::ROLE_ADMIN) && $geolocation->user_id != $request->user()->id )
			return $this->sendError('Invalid Request', 403);

		$geolocation->delete();

		return $this->sendResponse(true, 'Geo limitation deleted.');
	}
}
