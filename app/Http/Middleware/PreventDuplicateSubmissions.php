<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class PreventDuplicateSubmissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only apply to POST, PUT, PATCH requests (form submissions)
        if (in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            $token = $request->input('_submission_token') ?: $request->header('X-Submission-Token');

            // Only check for duplicates if a token is provided
            if ($token) {
                $sessionKey = 'submission_tokens';
                $tokens = Session::get($sessionKey, []);

                // Check if token was recently used (within last 5 seconds)
                if (isset($tokens[$token]) && (time() - $tokens[$token]) < 5) {
                    return response()->json(['error' => 'Please wait before submitting again'], 429);
                }

                // Clean old tokens (older than 10 minutes)
                $tokens = array_filter($tokens, function($timestamp) {
                    return (time() - $timestamp) < 600; // 10 minutes
                });

                // Store current token
                $tokens[$token] = time();
                Session::put($sessionKey, $tokens);
            }
            // If no token provided, allow the request to proceed without duplicate checking
        }

        return $next($request);
    }
}
