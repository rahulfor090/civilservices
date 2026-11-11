<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'role_slug',
        'module',
        'action',
        'auditable_type',
        'auditable_id',
        'before_json',
        'after_json',
        'ip',
        'user_agent',
        'hash',
        'previous_hash',
        'created_at',
    ];

    protected $casts = [
        'before_json' => 'array',
        'after_json' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getRecordHashAttribute(): string
    {
        return $this->hash ?? '';
    }

    public function getPrevHashAttribute(): ?string
    {
        return $this->previous_hash;
    }

    /**
     * Create a tamper-evident audit record with SHA-256 hash chaining
     */
    public static function record(
        ?User $user,
        string $module,
        string $action,
        ?Model $auditable = null,
        ?array $before = null,
        ?array $after = null
    ): self {
        $lastLog = self::latest('id')->first();
        $previousHash = $lastLog ? $lastLog->hash : 'GENESIS_BLOCK_CIVILSERVICEGURUKUL';

        $roleSlug = $user ? ($user->roles->first()?->slug ?? 'authenticated') : 'guest';

        $payload = [
            'user_id' => $user?->id,
            'role_slug' => $roleSlug,
            'module' => $module,
            'action' => $action,
            'auditable_type' => $auditable ? get_class($auditable) : null,
            'auditable_id' => $auditable?->id,
            'before' => $before,
            'after' => $after,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ];

        $currentHash = hash('sha256', $previousHash . json_encode($payload));

        return self::create([
            'user_id' => $user?->id,
            'role_slug' => $roleSlug,
            'module' => $module,
            'action' => $action,
            'auditable_type' => $payload['auditable_type'],
            'auditable_id' => $payload['auditable_id'],
            'before_json' => $before,
            'after_json' => $after,
            'ip' => $payload['ip'],
            'user_agent' => $payload['user_agent'],
            'hash' => $currentHash,
            'previous_hash' => $previousHash,
            'created_at' => now(),
        ]);
    }
}
