<?php

namespace App\Http\Controllers;

use InfyOm\Generator\Utils\ResponseUtil;

class AppBaseController extends Controller
{
    public function sendResponse($result, $message = '')
    {
        return response()->json(ResponseUtil::makeResponse($message, $result));
    }

    public function sendError($error, $code = 404)
    {
        return response()->json($error, $code);
    }
}
