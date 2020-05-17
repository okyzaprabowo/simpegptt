<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

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
        $isApi = $request->expectsJson() || $request->wantsJson() || $request->ajax();
                   
        if ($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            if($isApi){ 
                return response()->json(['status'=>404,'message'=>__('alert.resource_not_found'),'data'=>null,'errors'=>[true]],404);
            }else{
                return response()->view('error.generic',['message'=>__('alert.resource_not_found'),'code'=>404]);
            }
        }else if($exception instanceof \Illuminate\Auth\AuthenticationException){
            if($isApi){ 
                return response()->json(['status'=>401,'message'=>__('alert.invalid_token'),'data'=>null,'errors'=>[true]],401);
            }else{
                // return response()->view('error.generic',['message'=>__('alert.invalid_token'),'code'=>401]);
            }
        }else if($exception instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException){
            if($isApi){ 
                return response()->json(['status'=>405,'message'=>__('alert.resource_not_found'),'data'=>null,'errors'=>[true]],405);
            }else{
                return response()->view('error.generic',['message'=>__('alert.resource_not_found'),'code'=>404]);
            }
        }
        

        return parent::render($request, $exception);
    }
}
