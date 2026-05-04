<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Throwable;

class ActivityLogger
{
    public static function log(
        string $action,
        ?Model $subject = null,
        ?string $description = null,
        array $properties = [],
        ?Authenticatable $user = null
    ): void {
        $request = request();

        $payload = [
            'user_id' => $user?->getAuthIdentifier(),
            'action' => $action,
            'description' => $description,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'properties' => $properties,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
        ];

        try {
            ActivityLog::create($payload);
        } catch (Throwable $exception) {
            Log::channel('activity')->warning('Activity log database write failed', [
                'action' => $action,
                'error' => $exception->getMessage(),
            ]);
        }

        Log::channel('activity')->info($description ?? $action, [
            'user_id' => $payload['user_id'],
            'action' => $action,
            'subject_type' => $payload['subject_type'],
            'subject_id' => $payload['subject_id'],
            'ip_address' => $payload['ip_address'],
            'properties' => $properties,
        ]);
    }
}
