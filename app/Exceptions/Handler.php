<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Auth;
use Illuminate\Routing\Route;
use Response;
use InfyOm\Generator\Utils\ResponseUtil;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
		if($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException){
			if (preg_match('/backend/', $request->path())) {
				return redirect()->route( 'login' );
			}elseif (!Auth::user()){
				return redirect()->route('frontend.login');
			}
		}

		if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
			if ($request->isXmlHttpRequest()) {
				return Response::json(ResponseUtil::makeError('Reload the page and try again please'), 400);
			}else{
				return redirect()->route('login');
			}
		}

        return parent::render($request, $exception);
    }
}
