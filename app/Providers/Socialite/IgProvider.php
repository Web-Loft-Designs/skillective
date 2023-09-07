<?php

namespace App\Providers\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

/*
 * New Ig Basic Api
 */
class IgProvider extends AbstractProvider implements ProviderInterface
{


	protected $scopes = [
		'user_profile',
		'user_media'
	];

	protected $scopeSeparator = ',';

	protected function getCodeFields($state = null)
	{
		$fields = [
			'app_id' => $this->clientId,
			'redirect_uri' => $this->redirectUrl,
			'scope' => $this->formatScopes($this->getScopes(), $this->scopeSeparator),
			'response_type' => 'code',
		];

		if ($this->usesState()) {
			$fields['state'] = $state;
		}

		return array_merge($fields, $this->parameters);
	}

	protected function getAuthUrl($state)
	{
		return $this->buildAuthUrlFromBase('https://api.instagram.com/oauth/authorize', $state); // ?app_id={app-id}&redirect_uri={redirect-uri}&scope=user_profile,user_media&response_type=code
	}

	protected function getTokenUrl()
	{
		return 'https://api.instagram.com/oauth/access_token';
	}

	public function getAccessToken($code)
	{
		$response = $this->getHttpClient()->post($this->getTokenUrl(), [
			'body'    => $this->getTokenFields($code),
		]);

		return $this->parseAccessToken($response->getBody());
	}

	protected function getTokenFields($code)
	{
		return $tokenFields = [
			'app_id'		=> $this->clientId,
			'app_secret'	=> $this->clientSecret,
			'code'			=> $code,
			'redirect_uri'	=> $this->redirectUrl,
			'grant_type'	=> 'authorization_code'
		];
	}

	protected function getUserByToken($token)
	{
		$response = $this->getHttpClient()->get("https://graph.instagram.com/me?fields=id,username&access_token=$token");
		return json_decode($response->getBody(), true);
	}

	protected function mapUserToObject(array $user)
	{
		return (new User)->setRaw($user)->map([
			'id'       => $user['id'],
			'nickname' => $user['username'],
			'name'     => $user['username']
		]);
	}
}
