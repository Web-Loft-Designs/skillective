<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Facades\InstagramLoader;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Log;

class LoadInstagramMediaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user_id = null; // user id

	public $tries = 1;

	public $timeout = 300;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user_id = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
	public function handle(UserRepository $userRepository)
	{
		$user = $userRepository->findWithoutFail($this->user_id);
		if ($user && $user->profile->instagram_token!=null){

			$recentMedia = InstagramLoader::getOwnRecentMediaUrls($user->profile->instagram_token, $user->profile->instagram_handle);
			if (is_array($recentMedia)){
				$recentMedia = array_reverse($recentMedia);
				foreach ($recentMedia as $m){

					Log::channel('instagram')->info("USER {$this->user_id}: {$m->media_url}");

					if (isset($m->media_url)) {
						// do not load already loaded images
						$mediaBasename = basename($m->media_url);
						$filename = substr($mediaBasename, 0, strpos($mediaBasename, '?'));
						$mediaExists = $user->media()->where('collection_name', 'instagram')->where('file_name', $filename)->count();

                        if ($mediaExists == 0) {
                            try {
                                $user->addInstagramMedia($m->media_url);
                            } catch (Exception $e) {
                                Log::error('Load from IG Error: ' . $e->getMessage());
                            }
                        }
					}
				}
			}

			$user->profile->instagram_token = null;
			$user->profile->save();
		}
	}
}
