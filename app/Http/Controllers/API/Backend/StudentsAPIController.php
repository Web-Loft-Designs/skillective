<?php

namespace App\Http\Controllers\API\Backend;

use App\Http\Controllers\API\Backend\UsersAPIController;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Requests\API\CancelUsersAPIRequest;
use Illuminate\Http\Request;
use Response;
use Auth;

class StudentsAPIController extends UsersAPIController
{
    public function index(Request $request)
    {
		$this->userRepository->setPresenter("App\\Presenters\\StudentsInListPresenter");
		$roleID = Role::findByName(User::ROLE_STUDENT)->id;
		$users = $this->userRepository->presentResponse( $this->userRepository->getUsers($request, $roleID) );

        return $this->sendResponse($users);
    }

}
