<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class GenreController
 * @package App\Http\Controllers\API
 */

class USStatesAPIController extends AppBaseController
{

    public function index(Request $request)
    {
		return getUSStates();
	}
}
