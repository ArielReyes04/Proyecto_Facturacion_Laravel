<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

/**
 * @property User|null $user
 * @property User|null $admin
 */
class AuditLog extends Model
{
    /**
     * Motivo del cambio (eliminación o desactivación)
     *
     * @var string|null
     */
    public $reason;
    protected $fillable = [
        'user_id',
        'admin_id',
        'action',
        'table_name',
        'record_id',
        'old_values',
        'new_values',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    /**
     * Get the user that performed the action.
     *
     * @return BelongsTo<User, AuditLog>
     */
    public function user(): BelongsTo
    {
        /** @var BelongsTo<User, AuditLog> */
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the admin related to the action.
     *
     * @return BelongsTo<User, AuditLog>
     */
    public function admin(): BelongsTo
    {
        /** @var BelongsTo<User, AuditLog> */
        return $this->belongsTo(User::class, 'admin_id');
    }
}
