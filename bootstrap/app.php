<?php

use App\Http\Middleware\ActivityLogMiddleware;
use App\Http\Middleware\PreventDuplicateSubmissions;
use App\Http\Middleware\Roles;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->use([ActivityLogMiddleware::class]);
        $middleware->alias([
            'role' => Roles::class,
            'prevent.duplicate' => PreventDuplicateSubmissions::class,
        ]);
    })
    // ->withExceptions(function (Exceptions $exceptions): void {
    //     $exceptions->render(function (\Throwable $e, $request) {
    //         // Handle authentication/session errors
    //         if ($e instanceof \Illuminate\Auth\AuthenticationException ||
    //             $e instanceof \Illuminate\Auth\Access\AuthorizationException ||
    //             $e instanceof \Illuminate\Session\TokenMismatchException ||
    //             $e->getMessage() === 'Unauthenticated.' ||
    //             str_contains($e->getMessage(), 'session') ||
    //             str_contains($e->getMessage(), 'expired')) {

    //             if ($request->expectsJson()) {
    //                 return response()->json(['error' => 'Session expired. Please login again.'], 401);
    //             }

    //             // Return simple HTML with SweetAlert
    //             $html = '
    //             <!DOCTYPE html>
    //             <html>
    //             <head>
    //                 <title>Session Expired</title>
    //                 <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    //             </head>
    //             <body>
    //                 <script>
    //                     Swal.fire({
    //                         icon: "warning",
    //                         title: "Session Expired",
    //                         text: "Your session has expired. Redirecting to login...",
    //                         confirmButtonText: "OK",
    //                         allowOutsideClick: false,
    //                         allowEscapeKey: false,
    //                         showConfirmButton: false,
    //                         timer: 3000,
    //                         timerProgressBar: true
    //                     }).then(() => {
    //                         window.location.href = "/login";
    //                     });
    //                     setTimeout(() => {
    //                         window.location.href = "/login";
    //                     }, 3000);
    //                 </script>
    //             </body>
    //             </html>';

    //             return response($html, 401);
    //         }

    //         // Handle validation errors
    //         if ($e instanceof \Illuminate\Validation\ValidationException) {
    //             if ($request->expectsJson()) {
    //                 return response()->json([
    //                     'message' => 'Validation failed',
    //                     'errors' => $e->errors()
    //                 ], 422);
    //             }

    //             $errorList = '';
    //             foreach ($e->errors() as $field => $fieldErrors) {
    //                 foreach ($fieldErrors as $error) {
    //                     $errorList .= '<li>' . htmlspecialchars($error) . '</li>';
    //                 }
    //             }

    //             $html = '
    //             <!DOCTYPE html>
    //             <html>
    //             <head>
    //                 <title>Validation Error</title>
    //                 <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    //             </head>
    //             <body>
    //                 <div style="padding: 20px; text-align: center;">
    //                     <h3>Validation Error</h3>
    //                     <p>Please check your input and try again.</p>
    //                     <ul style="text-align: left; max-width: 600px; margin: 20px auto;">' . $errorList . '</ul>
    //                     <button onclick="history.back()" class="btn btn-primary">Go Back</button>
    //                 </div>
    //                 <script>
    //                     Swal.fire({
    //                         icon: "error",
    //                         title: "Validation Error",
    //                         text: "Please check your input and try again.",
    //                         confirmButtonText: "OK"
    //                     });
    //                 </script>
    //             </body>
    //             </html>';

    //             return response($html, 422);
    //         }

    //         // Handle model not found
    //         if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
    //             if ($request->expectsJson()) {
    //                 return response()->json(['error' => 'Resource not found'], 404);
    //             }

    //             $html = '
    //             <!DOCTYPE html>
    //             <html>
    //             <head>
    //                 <title>Not Found</title>
    //                 <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    //             </head>
    //             <body>
    //                 <div style="padding: 20px; text-align: center;">
    //                     <h3>Not Found</h3>
    //                     <p>The requested resource was not found.</p>
    //                     <button onclick="window.location.href=\'/login\'" class="btn btn-primary">Continue</button>
    //                 </div>
    //                 <script>
    //                     Swal.fire({
    //                         icon: "info",
    //                         title: "Not Found",
    //                         text: "The requested resource was not found.",
    //                         confirmButtonText: "OK"
    //                     });
    //                 </script>
    //             </body>
    //             </html>';

    //             return response($html, 404);
    //         }

    //         // Handle all other exceptions
    //         if ($request->expectsJson()) {
    //             return response()->json([
    //                 'error' => 'An error occurred',
    //                 'message' => 'Something went wrong. Please try again.'
    //             ], 500);
    //         }

    //         // For web requests, return simple HTML with SweetAlert
    //         $html = '
    //         <!DOCTYPE html>
    //         <html>
    //         <head>
    //             <title>Error</title>
    //             <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    //         </head>
    //         <body>
    //             <div style="padding: 20px; text-align: center;">
    //                 <h3>Error</h3>
    //                 <p>Something went wrong. Please try again.</p>
    //                 <button onclick="window.location.href=\'/login\'" class="btn btn-primary">Continue</button>
    //                 <button onclick="history.back()" class="btn btn-secondary" style="margin-left: 10px;">Go Back</button>
    //             </div>
    //             <script>
    //                 Swal.fire({
    //                     icon: "error",
    //                     title: "Error",
    //                     text: "Something went wrong. Please try again.",
    //                     confirmButtonText: "OK"
    //                 });
    //             </script>
    //         </body>
    //         </html>';

    //         return response($html, 500);
    //     });
    // })->create();
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (\Throwable $e, $request) {

            /*
            |--------------------------------------------------------------------------
            | Authentication / Session Expired
            |--------------------------------------------------------------------------
            */
            if ($e instanceof \Illuminate\Auth\AuthenticationException ||
                $e instanceof \Illuminate\Session\TokenMismatchException) {

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Session expired. Please login again.'
                    ], 401);
                }

                return redirect()
                    ->guest(route('login'))
                    ->with('error', 'Session expired. Please login again.');
            }


            /*
            |--------------------------------------------------------------------------
            | Authorization (403)
            |--------------------------------------------------------------------------
            */
            if ($e instanceof \Illuminate\Auth\Access\AuthorizationException) {

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'You are not authorized.'
                    ], 403);
                }

                return redirect()
                    ->back()
                    ->with('error', 'You are not authorized to perform this action.');
            }


            /*
            |--------------------------------------------------------------------------
            | Validation Error
            |--------------------------------------------------------------------------
            */
            if ($e instanceof \Illuminate\Validation\ValidationException) {

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Validation failed',
                        'errors' => $e->errors()
                    ], 422);
                }

                return redirect()
                    ->back()
                    ->withErrors($e->errors())
                    ->withInput();
            }


            /*
            |--------------------------------------------------------------------------
            | Model Not Found
            |--------------------------------------------------------------------------
            */
            if ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {

                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Resource not found.'
                    ], 404);
                }

                return redirect()
                    ->back()
                    ->with('error', 'The requested resource was not found.');
            }


            /*
            |--------------------------------------------------------------------------
            | Fallback Error
            |--------------------------------------------------------------------------
            */
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Something went wrong.'
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'Something went wrong. Please try again.');
        });

    })
    ->create();
