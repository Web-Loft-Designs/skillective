<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use InfyOm\Generator\Utils\ResponseUtil;
use Throwable;

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
     * @param Throwable $exception
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }


    /**
     * @param $request
     * @param Throwable $exception
     * @return JsonResponse|RedirectResponse|Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
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
