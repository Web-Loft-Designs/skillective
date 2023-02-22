<?php

namespace App\Services;


use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\GuzzleException;
use Http\Message\RequestFactory;
use Illuminate\Support\Facades\Log;


class InstagramLoader
{
    protected $requestFactory;

    public function __construct( RequestFactory $requestFactory = null)
    {
        $this->requestFactory = $requestFactory ?: new GuzzleHttpClient;
    }

    /**
     * @param string $accessToken
     * @param string $instagramHandle
     * @return array
     */
    public function getOwnRecentMediaUrls(string $accessToken, string $instagramHandle): array
    {
		$ownMedia = [];
        try{
			$url = sprintf('https://graph.instagram.com/me/media?fields=thumbnail_url,media_url,media_type&access_token=%s', $accessToken);
			$mediaList = $this->_makeRequest($url); // [{media_url, media_type, id}]
			if (isset($mediaList->data) && is_array($mediaList->data)){
				Log::channel('instagram')->info("Response Media List", $mediaList->data);
				foreach ($mediaList->data as $media){
					try {
						if (in_array($media->media_type, ['VIDEO', 'IMAGE'])){
							$url = sprintf('https://graph.instagram.com/%s?fields=id,media_type,media_url,username,thumbnail_url&access_token=%s', $media->id, $accessToken);
							$mediaItem = $this->_makeRequest( $url );
							if ($mediaItem->username==$instagramHandle)
								$ownMedia[] = $mediaItem;
						}elseif('CAROUSEL_ALBUM'==$media->media_type){
							$url = sprintf('https://graph.instagram.com/%s/children?fields=id,media_type,media_url,username,thumbnail_url&access_token=%s', $media->id, $accessToken);
							$childMediaItemsList = $this->_makeRequest( $url );
							foreach ($childMediaItemsList->data as $childMedia){
								try {
									if (in_array($childMedia->media_type, ['VIDEO', 'IMAGE'])){
										$url = sprintf('https://graph.instagram.com/%s?fields=id,media_type,media_url,username,thumbnail_url&access_token=%s', $childMedia->id, $accessToken);
										$mediaItem = $this->_makeRequest( $url );
										if ($mediaItem->username==$instagramHandle)
											$ownMedia[] = $mediaItem;
									}
								}catch (\Exception $e){
									Log::error('Can\'t load user media with ID ' . $childMedia->id);
								}
							}
						}
					}catch (\Exception $e){
						Log::error('Can\'t load user media with ID ' . $media->id);
					}
				}
			}
		}catch (\Exception $e){
			Log::error('Can\'t load user media list from IG:' . $e->getMessage());
		}
        return $ownMedia;
    }

    /**
     * @param $url
     * @return mixed
     * @throws GuzzleException
     */
    private function _makeRequest($url)
    {
		$response = $this->requestFactory->request( 'GET', $url);

		if ($response->getStatusCode() === 400) {
			$body = json_decode((string) $response->getBody());
			Log::error($response->getBody());
			throw new \Exception($body->meta->error_message);
		}
		return json_decode((string) $response->getBody());
	}
}
