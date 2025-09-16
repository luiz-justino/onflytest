<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TravelOrder extends Model
{
    use HasFactory;

    protected $table = 'travel_orders';

    protected $fillable = [
        'order_id',
        'user_id',
        'requester_name',
        'destination',
        'departure_date',
        'return_date',
        'status',
        'cancellation_reason',
        'approved_at',
        'cancelled_at',
    ];

    protected $dates = [
        'departure_date',
        'return_date',
        'approved_at',
        'cancelled_at',
        'created_at',
        'updated_at',
    ];

    // Status possíveis
    const STATUS_REQUESTED = 'solicitado';
    const STATUS_APPROVED = 'aprovado';
    const STATUS_CANCELLED = 'cancelado';

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Boot method para gerar order_id automaticamente
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($travelOrder) {
            if (empty($travelOrder->order_id)) {
                $travelOrder->order_id = 'TRV-' . strtoupper(uniqid());
            }
        });
    }

    /**
     * Scope para filtrar por status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope para filtrar por período
     */
    public function scopeByPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('departure_date', [$startDate, $endDate])
                    ->orWhereBetween('return_date', [$startDate, $endDate]);
    }

    /**
     * Scope para filtrar por destino
     */
    public function scopeByDestination($query, $destination)
    {
        return $query->where('destination', 'like', '%' . $destination . '%');
    }

    /**
     * Verificar se o pedido pode ser cancelado
     */
    public function canBeCancelled(): bool
    {
        return $this->status === self::STATUS_APPROVED &&
               Carbon::createFromFormat('d/m/Y', $this->departure_date)->gt(now());
    }

    /**
     * Verificar se o pedido pode ser aprovado
     */
    public function canBeApproved(): bool
    {
        return $this->status === self::STATUS_REQUESTED;
    }

    /**
     * Verificar se o pedido pode ser cancelado pelo usuário
     */
    public function canBeCancelledByUser(): bool
    {
        return $this->status === self::STATUS_REQUESTED;
    }

    /**
     * Aprovar pedido
     */
    public function approve(): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->status = self::STATUS_APPROVED;
        $this->approved_at = now();
        return $this->save();
    }

    /**
     * Cancelar pedido
     */
    public function cancel(string $reason = null): bool
    {
        $this->status = self::STATUS_CANCELLED;
        $this->cancelled_at = now();
        $this->cancellation_reason = $reason;
        return $this->save();
    }

    // Accessors para exibir datas no formato d/m/Y
    public function getDepartureDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d/m/Y') : null;
    }

    public function getReturnDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('d/m/Y') : null;
    }

    // Mutators para salvar datas no formato Y-m-d
    public function setDepartureDateAttribute($value)
    {
        $this->attributes['departure_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function setReturnDateAttribute($value)
    {
        $this->attributes['return_date'] = $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }
}