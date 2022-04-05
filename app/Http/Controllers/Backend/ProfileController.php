<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use App\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use Session;
use Auth;

class ProfileController extends Controller
{
	/** @var  UserRepository */
	private $userRepository;

	public function __construct(UserRepository $userRepo)
	{
		$this->userRepository	= $userRepo;

		parent::__construct();
	}

	public function edit(Request $request)
	{
		$user = Auth::user();

		$userData = $this->userRepository->getUserData($user->id);
		$userData = $this->userRepository->presentResponse($userData)['data'];

		return view("backend.admin.profile-edit", [
			'page_title'		=> "My Profile",
			'userProfileData'	=> $userData
		]);
	}
}
