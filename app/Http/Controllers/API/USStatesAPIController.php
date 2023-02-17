<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;


class USStatesAPIController extends AppBaseController
{

    /**
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
		return getUSStates();
	}
}
