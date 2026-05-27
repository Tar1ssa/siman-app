<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PreventDuplicateSubmissions
{
    private const LOCK_SECONDS = 5;

    private array $excludedRouteNames = [
        'actionLogin',
        'logout',
        'test-dashboard.run',
        'activity-logs.cleanup',
        'settings.update',
        'internal.store',
        'siman.store',
        'internal.addImage',
        'internal.addDocument',
        'psp.download',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();
        if ($routeName && in_array($routeName, $this->excludedRouteNames, true)) {
            return $next($request);
        }

        $fingerprint = $this->buildFingerprint($request);
        $lockKey = 'duplicate_submission:' . hash('sha256', $fingerprint);

        if (! Cache::add($lockKey, now()->timestamp, self::LOCK_SECONDS)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permintaan duplikat terdeteksi. Mohon tunggu sebentar.',
                ], 429);
            }

            return redirect()
                ->back()
                ->with('warning', 'Permintaan duplikat terdeteksi. Mohon tunggu sebentar.');
        }

        return $next($request);
    }

    private function buildFingerprint(Request $request): string
    {
        $userKey = $request->user()?->id ?? ('guest:' . $request->ip());
        $method = strtoupper($request->method());
        $path = $request->path();

        $token = $request->input('_submission_token') ?: $request->header('X-Submission-Token');

        if (! empty($token)) {
            return implode('|', [$userKey, $method, $path, 'token:' . $token]);
        }

        $payload = $request->except([
            '_token',
            '_method',
            '_submission_token',
        ]);

        ksort($payload);

        $files = [];
        foreach ($request->allFiles() as $field => $file) {
            if (is_array($file)) {
                foreach ($file as $index => $item) {
                    if ($item) {
                        $files[] = $field . ':' . $index . ':' . $item->getClientOriginalName() . ':' . $item->getSize();
                    }
                }
                continue;
            }

            if ($file) {
                $files[] = $field . ':' . $file->getClientOriginalName() . ':' . $file->getSize();
            }
        }

        sort($files);

        return implode('|', [
            $userKey,
            $method,
            $path,
            json_encode($payload),
            implode(',', $files),
        ]);
    }
}
