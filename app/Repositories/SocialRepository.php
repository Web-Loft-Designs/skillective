<?php

namespace App\Repositories;

use App\Models\Social;
use Illuminate\Support\Facades\Auth;


class SocialRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'user_id',
        'provider',
        'social_id'
    ];


    /**
     * @return string
     */
    public function model()
    {
        return Social::class;
    }

    /**
     * @param $socialId
     * @param $provider
     * @return mixed
     */
    public function getBySocialIdAndProvider($socialId, $provider){
		return $this->model->where('social_id', $socialId)->where('provider', $provider)->first();
	}

    /**
     * @param $socialUserObject
     * @param $provider
     * @param $user
     * @return bool
     */
    public function createAndAttachToUser($socialUserObject, $provider, $user = null){
		$socialAlreadyLinked = false;
		$user = !$user ? Auth::user() : $user;

		foreach ($user->social as $userSocial) {
			if ($userSocial->provider == $provider && $userSocial->social_id == $socialUserObject->id) {
				$socialAlreadyLinked = true;
				break;
			}
		}
		if ( !$socialAlreadyLinked ) {
			if (Social::where('provider', '=', $provider)->where('social_id', '=', $socialUserObject->id)->get()->count() == 0) {
				$socialData = new Social();
				$socialData->social_id = $socialUserObject->id;
				$socialData->provider = $provider;
				$user->social()->save($socialData);
				if ($provider=='ig' || $provider=='instagram'){
					$user->profile->instagram_handle			= $socialUserObject->user['username'];
					$user->profile->instagram_token			= $socialUserObject->token;
					$user->profile->save();
				}
				$socialAlreadyLinked = true;
			}
		}

		return $socialAlreadyLinked;
	}
}
