<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\AppBaseController;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Flash;
use Auth;
use Response;
use App\Models\User;

class LoginAsController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
		parent::__construct();
    }

    public function loginAs(Request $request)
    {
		$user = $this->userRepository->find($request->input('user_id'));
		if ($user) {
			Auth::loginUsingId($user->id, true);
			return redirect( $user->hasRole( User::ROLE_INSTRUCTOR ) ? route( 'instructor.dashboard' ) : route( 'student.dashboard' ) );
		}else{
			return abort(404);
		}
    }
}
