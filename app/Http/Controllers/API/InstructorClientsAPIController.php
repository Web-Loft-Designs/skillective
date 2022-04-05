<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\AddClientsAPIRequest;
use App\Http\Requests\API\RemoveClientsAPIRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Auth;
use Log;

/**
 * Class ClientController
 * @package App\Http\Controllers\API
 */

class InstructorClientsAPIController extends AppBaseController
{
    /** @var  UserRepository */
    private $userRepository;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the Client.
     * GET|HEAD /clients
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
		try{
			$clients = $this->userRepository->presentResponse($this->userRepository->getInstructorClients(Auth::user()->id, $request));
		}catch (\Exception $e){
			Log::error('getInstructorClients : ' . $e->getMessage());
			$clients = ['data'=>[]];
		}

        return $this->sendResponse($clients);
    }

    /**
     * Store a newly created Client in storage.
     * POST /clients
     *
     * @param AddClientsAPIRequest $request
     *
     * @return Response
     */
    public function add(AddClientsAPIRequest $request)
    {
    	$count_added = 0;
		foreach ($request->input('students') as $studentId){
			$student = $this->userRepository->find($studentId);
			if ($student->hasRole($this->userRepository->model()::ROLE_STUDENT)) {
				Auth::user()->clients()->attach( $studentId );
				$count_added++;
			}
		}

        return $this->sendResponse(true, $count_added . ' clients added');
    }

    /**
     * Remove the specified Client from storage.
     * DELETE /clients/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function remove($id)
    {
        /** @var Client $client */
        $client = $this->userRepository->findWithoutFail($id);

        if (empty($client)) {
            return $this->sendError('Client not found');
        }

        Auth::user()->clients()->detach($id);

        return $this->sendResponse(true, 'Client deleted');
    }

	public function removeMany(RemoveClientsAPIRequest $request)
	{
		$count_removed = 0;
		foreach ($request->input('clients') as $clientId){
			Auth::user()->clients()->detach($clientId);
			$count_removed++;
		}

		return $this->sendResponse(true, $count_removed . ' clients removed');
	}
}
