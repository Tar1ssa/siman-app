<?php

namespace App\Services;

use App\Models\ImportRun as ImportRunModel;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;

class ImportIdempotencyService
{
    public function fingerprintForFile(
        UploadedFile $file,
        ?string $batchLabel,
        string $source,
        ?int $userId = null
    ): string {
        $parts = [
            $source,
            $userId ?? 0,
            trim((string) $batchLabel),
            sha1_file($file->getRealPath()) ?: '',
        ];

        return hash('sha256', implode('|', $parts));
    }

    public function reserve(
        string $source,
        string $fingerprint,
        ?int $userId,
        ?string $batchLabel
    ): array {
        try {
            $existing = ImportRunModel::where('fingerprint', $fingerprint)->first();

            if ($existing && $existing->status === 'completed') {
                return [
                    'state' => 'completed',
                    'run' => $existing,
                    'response' => $existing->response_payload,
                    'response_status' => $existing->response_status ?? 200,
                ];
            }

            if ($existing && $existing->status === 'processing') {
                return [
                    'state' => 'busy',
                    'run' => $existing,
                ];
            }

            if ($existing) {
                $existing->forceFill([
                    'source' => $source,
                    'user_id' => $userId,
                    'batch_label' => $batchLabel,
                    'status' => 'processing',
                    'response_status' => null,
                    'response_payload' => null,
                    'error_message' => null,
                    'started_at' => now(),
                    'finished_at' => null,
                ])->save();

                return [
                    'state' => 'resumed',
                    'run' => $existing->fresh(),
                ];
            }

            $run = ImportRunModel::create([
                'source' => $source,
                'fingerprint' => $fingerprint,
                'user_id' => $userId,
                'batch_label' => $batchLabel,
                'status' => 'processing',
                'started_at' => now(),
            ]);

            return [
                'state' => 'new',
                'run' => $run,
            ];
        } catch (QueryException $exception) {
            if (! $this->isUniqueViolation($exception)) {
                throw $exception;
            }

            return $this->reserve($source, $fingerprint, $userId, $batchLabel);
        }
    }

    public function markCompleted(ImportRunModel $run, array $payload, int $statusCode = 200): void
    {
        $run->forceFill([
            'status' => 'completed',
            'response_status' => $statusCode,
            'response_payload' => $payload,
            'error_message' => null,
            'finished_at' => now(),
        ])->save();
    }

    public function markFailed(ImportRunModel $run, string $message, int $statusCode = 500): void
    {
        $run->forceFill([
            'status' => 'failed',
            'response_status' => $statusCode,
            'response_payload' => [
                'success' => false,
                'message' => $message,
            ],
            'error_message' => $message,
            'finished_at' => now(),
        ])->save();
    }

    private function isUniqueViolation(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        $driverCode = $exception->errorInfo[1] ?? null;

        return $sqlState === '23000' || $driverCode === 1062;
    }
}
