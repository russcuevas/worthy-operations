<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'module',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static array $resolvedCache = [];

    public function getFormattedPayloadAttribute(): array
    {
        if (!is_array($this->payload)) {
            return [];
        }

        $formatted = [];
        foreach ($this->payload as $key => $value) {
            if (is_null($value) || $value === '') {
                continue;
            }

            $label = match ($key) {
                'tx_code' => 'Tx Code',
                'sku' => 'SKU',
                'location_id', 'storage_location_id' => 'Location',
                'source_location', 'source_location_id' => 'Source Location',
                'destination_location_id' => 'Destination Location',
                'from_loc' => 'From Location',
                'to_loc' => 'To Location',
                'task_id' => 'Task',
                'task_code' => 'Task Code',
                'user_id' => 'User',
                'ip' => 'IP Address',
                default => ucwords(str_replace(['_', '-'], ' ', $key)),
            };

            $displayValue = $value;

            // Dynamically resolve real database records when an ID is present
            if (is_numeric($value)) {
                $id = (int)$value;
                if ($key === 'task_id') {
                    $cacheKey = "task_{$id}";
                    if (!isset(self::$resolvedCache[$cacheKey])) {
                        $task = StaffTask::find($id);
                        self::$resolvedCache[$cacheKey] = $task ? "{$task->task_code} - {$task->title}" : "Task #{$id}";
                    }
                    $displayValue = self::$resolvedCache[$cacheKey];
                } elseif ($key === 'user_id') {
                    $cacheKey = "user_{$id}";
                    if (!isset(self::$resolvedCache[$cacheKey])) {
                        $u = User::find($id);
                        self::$resolvedCache[$cacheKey] = $u ? "{$u->name} (@{$u->username})" : "User #{$id}";
                    }
                    $displayValue = self::$resolvedCache[$cacheKey];
                }
            }

            if (is_bool($displayValue)) {
                $displayValue = $displayValue ? 'Yes' : 'No';
            } elseif (is_array($displayValue)) {
                $displayValue = json_encode($displayValue);
            } else {
                $displayValue = (string) $displayValue;
            }

            $formatted[] = [
                'key' => $key,
                'label' => $label,
                'value' => $displayValue,
            ];
        }

        return $formatted;
    }

    public static function record(string $module, string $action, string $description, ?array $payload = null, ?int $userId = null): self
    {
        return self::create([
            'user_id' => $userId ?? Auth::id(),
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'payload' => $payload,
        ]);
    }
}
