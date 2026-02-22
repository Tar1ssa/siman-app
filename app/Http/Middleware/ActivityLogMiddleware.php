<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip logging for datatable requests to avoid recursion and performance issues
        if (str_contains($request->path(), 'datatable')) {
            return $next($request);
        }

        $response = $next($request);

        // Log the activity
        $this->logActivity($request, $response);

        return $response;
    }

    protected function logActivity(Request $request, Response $response)
    {
        $route = $request->route();
        $routeName = $route ? $route->getName() : null;
        $parameters = $route ? $route->parameters() : [];

        $statusCode = $response->getStatusCode();
        $responseContent = null;

        // For errors, log the raw response content for debugging
        if ($statusCode >= 400) {
            $responseContent = $response->getContent();
            // Truncate if too long
            if (strlen($responseContent) > 10000) {
                $responseContent = substr($responseContent, 0, 10000) . '...';
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'method' => $request->method(),
            'uri' => $request->fullUrl(),
            'route_name' => $routeName,
            'route_parameters' => $parameters,
            'status_code' => $statusCode,
            'response_content' => $responseContent,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
