<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\Validator;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($this->isHttpException($e)) {

            $statusCode = $e->getStatusCode();

            switch ($statusCode) {
                case '500':
                    return response()->view('layouts.error.500');
            }
        }
        return parent::render($request, $e);
    }

    public function renderHttpException(HttpExceptionInterface $e)
    {
        if ($e->getStatusCode() === 500) {
            // Display Laravel's default error message with appropriate error information
            return response()->view('layouts.error.500');
        }
        return parent::renderHttpException($e); // Continue as normal
    }

    public function failedValidation(Validator $validator)
    {
        return response()->json($validator->errors());
    }
}
