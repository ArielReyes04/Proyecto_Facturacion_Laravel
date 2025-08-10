<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Collection;

/**
 * App\Models\Invoice
 *
 * @property int $id
 * @property string $invoice_number
 * @property int $client_id
 * @property int $user_id
 * @property string|null $status
 * @property string|null $cancellation_reason
 * @property string|null $deletion_reason
 * @property \Carbon\Carbon|null $issue_date
 * @property \Carbon\Carbon|null $due_date
 * @property \Carbon\Carbon|null $cancelled_at
 * @property \Illuminate\Database\Eloquent\Collection|\App\Models\InvoiceItem[] $items
 * @property float $subtotal
 * @property float $tax
 * @property float $total
 * @property-read \App\Models\Client $client
 * @property-read \App\Models\User $user
 * @property-read \App\Models\User|null $cancelledBy
 * @property-read \App\Models\User|null $deletedBy
 * @property-read Collection<int, \App\Models\InvoiceItem> $items
 * @property-read int|null $items_count
 * 
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Invoice latest($column = 'id')
 */
class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'client_id',
        'user_id',
        'issue_date',
        'due_date',
        'subtotal',
        'tax',
        'total',
        'status',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'deletion_reason',
        'deleted_by',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
        'issue_date' => 'date',
        'due_date' => 'date',
        'cancelled_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'pendiente' || $this->status === 'pagado';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'cancelado';
    }

    public function canBeCancelledBy(User $user): bool
    {
        return $this->user_id === $user->id || $user->hasRole('Administrador');
    }

    public function canBeDeletedBy(User $user): bool
    {
        return $this->user_id === $user->id || $user->hasRole('Administrador');
    }

    public static function generateInvoiceNumber(): string
    {
        $lastInvoice = self::latest('id')->first();
        $number = $lastInvoice ? $lastInvoice->id + 1 : 1;
        return 'INV-' . str_pad((string)$number, 6, '0', STR_PAD_LEFT);
    }

    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }
}
