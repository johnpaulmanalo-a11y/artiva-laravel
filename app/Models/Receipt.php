<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Receipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_id',
        'receipt_number',   
        'issued_at',
        'email_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
            'email_sent_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public static function generateReceiptNumber(): string
    {
        do {
            $receiptNumber = 'ART-RCT-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
        } while (static::query()->where('receipt_number', $receiptNumber)->exists());

        return $receiptNumber;
    }
}
