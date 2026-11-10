<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobTicket extends Model
{
    protected $fillable = [
        'ticket_number', 'service_request_id', 'customer_id', 'technician_id', 'title', 'work_summary',
        'status', 'labor_hours', 'materials_cost', 'labor_rate', 'scheduled_at', 'completed_at', 'slug',
    ];

    protected function casts(): array
    {
        return [
            'labor_hours' => 'decimal:2',
            'materials_cost' => 'decimal:2',
            'labor_rate' => 'decimal:2',
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function technician(): BelongsTo
    {
        return $this->belongsTo(Technician::class);
    }

    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
