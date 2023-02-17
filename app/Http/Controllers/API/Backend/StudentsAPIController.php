<?php

namespace App\Http\Controllers\API\Backend;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Prettus\Repository\Exceptions\RepositoryException;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;


class StudentsAPIController extends UsersAPIController
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws RepositoryException
     */
    public function index(Request $request)
    {
		$this->userRepository->setPresenter("App\\Presenters\\StudentsInListPresenter");
		$roleID = Role::findByName(User::ROLE_STUDENT)->id;
		$users = $this->userRepository->presentResponse( $this->userRepository->getUsers($request, $roleID) );

        return $this->sendResponse($users);
    }

}
