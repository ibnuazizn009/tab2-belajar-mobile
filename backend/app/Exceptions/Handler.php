<?php

namespace App\Exceptions;

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
        'current_password',
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

    /**
     * Memaksa Laravel membuang render HTML View dan mengubahnya menjadi JSON rapi.
     */
    public function render($request, Throwable $exception)
    {
        // Set header response ke JSON menggunakan PHP Native
        header('Content-Type: application/json; charset=utf-8');
        http_response_code(500);

        // Keluarkan teks JSON murni tanpa bantuan helper Laravel
        echo json_encode([
            'error'   => true,
            'message' => $exception->getMessage(),
            'file'    => $exception->getFile(),
            'line'    => $exception->getLine(),
            'code'    => $exception->getCode()
        ]);
        
        // Hentikan paksa proses agar Laravel tidak mencari komponen [view]
        die();
    }
}
