<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReminder extends Model
{
    protected $fillable = [
        'invoice_id', 'customer_id', 'channel', 'status', 'subject', 'body', 'ai_generated',
        'consent_verified', 'approved_at', 'approved_by', 'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'ai_generated' => 'boolean',
            'consent_verified' => 'boolean',
            'approved_at' => 'datetime',
            'sent_at' => 'datetime',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
