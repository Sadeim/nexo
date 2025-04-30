<?php

namespace App\Exceptions;

use App\Models\Course;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [

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
        if ($e instanceof ModelNotFoundException) {
            $class = match ($e->getModel()) {
                Course::class => 'User',
                default => 'Record',
            };
            return response()->view ('not_found', ['class' => $class], status: 404);
        }
        return parent::render($request, $e);
    }

    public function report(Throwable $e)
    {
        if ($e instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect()->route('login');
            // return response()->json(['login_modal' => true], 401);
        }
    }
}
