<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Pago extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id',
        'payment_type',
        'amount',
        'transaction_number',
        'observations',
        'status',
        'paid_by',
        'validated_by',
        'validated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'payment_type' => 'string',
        'amount' => 'decimal:2',
        'status' => 'string',
        'validated_at' => 'datetime',
    ];

    /**
     * Get the invoice that owns the payment.
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    /**
     * Get the user who made the payment.
     */
    public function payer()
    {
        return $this->belongsTo(Client::class, 'paid_by');
    }

    /**
     * Get the user who validated the payment.
     */
    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }
}