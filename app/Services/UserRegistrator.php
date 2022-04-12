<?php
namespace App\Services;

use App\Http\Requests\StudentSmallRegisterRequest;
use App\Models\User;
use App\Models\Invitation;
use App\Models\Profile;
use App\Models\UserGeoLocation;
use Spatie\Permission\Models\Role;
use App\Http\Requests\InstructorRegisterRequest;
use App\Http\Requests\API\StudentRegisterRequest;
use Illuminate\Auth\Events\Registered;
use App\Repositories\UserRepository;
use DB;
use Log;
use Illuminate\Support\Str;



class UserRegistrator {
	public function registerInstructor(InstructorRegisterRequest $request)
	{
		$inputData = $request->all();
		$inputData['password'] = 'skillectivefake-' . Str::random(60);
		event(new Registered($instructor = $this->createInstructor($inputData)));

		return $instructor;
	}

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return \App\Models\User
	 */
	protected function createInstructor(array $data)
	{
		\DB::beginTransaction();
		try {
			$userData = [
				'first_name' => $data['first_name'],
				'last_name'  => $data['last_name'],
				'email'      => $data['email'],
				'password'   => $data['password'] // Str::random(60)
			];

			if (isset($data['invitation']) && $data['invitation']){
				$tokenMatchingInvitation = Invitation::select('invitations.*')
													 ->whereNull('invited_user_id')
													 ->where('invitation_token', $data['invitation'])
													 ->with(['sender'])
													 ->first();
				if ($tokenMatchingInvitation){
					$userData['accepted_invitation_id'] = $tokenMatchingInvitation->id;
					$tokenMatchingInvitation->invitation_token = null;
					$tokenMatchingInvitation->save();
				}
			}

			$user = User::create( $userData );

			$userRole = Role::findByName( User::ROLE_INSTRUCTOR );
			$user->assignRole( $userRole );

			$profile = new Profile( [
				'address'			=> $data['address'],
				'city'				=> $data['city'],
				'state'				=> $data['state'],
				'dob'				=> $data['dob'],
				'zip'				=> $data['zip'],
				'mobile_phone'		=> $data['mobile_phone'],
				'about_me'			=> $data['about_me'],
				'gender'			=> isset($data['gender'])?$data['gender']:'',
			] );
			$user->profile()->save($profile);

			$user->genres()->sync($data['genres']);

			$user->setStatus(User::STATUS_ON_REVIEW);

//			$invitationToken = (isset($data['invitation']) && $data['invitation']) ? $data['invitation'] : null;
//			$this->_addFavoriteClientsAndInstructorsFromInvitations($user, $invitationToken);

			DB::commit();
		} catch (\Exception $e) {
			\DB::rollback();
			throw $e;
		}

		return $user;
	}

	/* registration using instagram */
	public function registerStudent(StudentRegisterRequest $request)
	{
		$inputData = $request->all();
		event(new Registered($student = $this->createStudent($inputData)));
		return $student;
	}

    // student must finish registration
    public function registerInactiveStudent($request)
    {
        $inputData = $request->all();

        $pass = Str::random(60);
        $inputData['password'] = $pass;
        $inputData['password_confirm'] = $pass;
        $inputData['finish_registration_token'] = Str::random(60);

        event(new Registered($student = $this->createStudent($inputData, User::STATUS_APPROVED)));

        return $student;
    }

	/**
	 * Create a new user instance after a valid registration.
	 *
	 * @param  array  $data
	 * @return \App\Models\User
	 */
	protected function createStudent(array $data, $status = null)
	{
		if (!$status)
			$status = User::STATUS_ACTIVE;

		\DB::beginTransaction();
		try {
			$userData = [
				'first_name' => $data['first_name'],
				'last_name'  => $data['last_name'],
				'email'      => $data['email'],
				'password'   => $data['password']
			];

			if (isset($data['invitation']) && $data['invitation']){
				$tokenMatchingInvitation = Invitation::select('invitations.*')
													 ->whereNull('invited_user_id')
													 ->where('invitation_token', $data['invitation'])
													 ->with(['sender'])
													 ->first();
				if ($tokenMatchingInvitation){
					$userData['accepted_invitation_id'] = $tokenMatchingInvitation->id;
					$tokenMatchingInvitation->invitation_token = null;
					$tokenMatchingInvitation->save();
				}
			}

			$user = User::create( $userData );

			if (isset($data['finish_registration_token']))
				$user->setFinishRegistrationToken($data['finish_registration_token']);

			$userRole = Role::findByName( User::ROLE_STUDENT );
			$user->assignRole( $userRole );

			$profile = new Profile( [
				'address'			=> isset($data['address'])?$data['address']:'',
				'city'				=> isset($data['city'])?$data['city']:'',
				'state'				=> isset($data['state'])?$data['state']:'',
				'dob'				=> isset($data['dob'])?$data['dob']:null,
				'zip'				=> isset($data['zip'])?$data['zip']:'',
				'mobile_phone'		=> isset($data['mobile_phone'])?$data['mobile_phone']:'',
				'about_me'			=> isset($data['about_me'])?$data['about_me']:'',
				'gender'			=> isset($data['gender'])?$data['gender']:'',
				'instagram_handle'	=> isset($data['instagram_handle'])?$data['instagram_handle']:'',
			] );
			$user->profile()->save($profile);

			$locationDetails = getLocationDetails($profile->city . ', ' . $profile->state . ' USA');
			if ( $locationDetails['city']!=null && $locationDetails['lat']!=null && $locationDetails['lng']!=null ){
				$geoLocation = new UserGeoLocation([
					'location'	=> $data['city'] . ', ' . $data['state'] . ' USA',
					'limit'	=> UserGeoLocation::getDefaultLimit(),
					'date_from' => now(),
					'date_to' => '01-01-2050'
				]);
				$user->geoLocations()->save($geoLocation);
			}

			if (isset($data['genres']))
				$user->genres()->sync($data['genres']);

			$user->setStatus($status);

			DB::commit();
		} catch (\Exception $e) {
			\DB::rollback();
			throw $e;
		}

		return $user;
	}
}
