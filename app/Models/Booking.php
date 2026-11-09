<?php

namespace App\Models;

use App\Http\Resources\Admin\BookingResource;
use App\Observers\BookingObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(BookingObserver::class)]
class Booking extends Model
{
    use HasFactory;

    public $resource = BookingResource::class;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'persons',
        'date',
        'time',
        'message',
        'status',
        'service_id',
        'barber_id',
        'reminder_sent_at',
    ];

    protected $casts = [
        'date' => 'date',
        'reminder_sent_at' => 'datetime',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /** The requested barber, or null for "any available barber". */
    public function barber()
    {
        return $this->belongsTo(Employee::class, 'barber_id');
    }

    /** A booking in this state has released its slot. */
    /**
     * Everything belonging to one barber, and nothing else.
     *
     * The portal's isolation rests entirely on this scope: every /me query
     * goes through it, and no portal route ever takes an id from the request.
     * It refuses an empty id rather than quietly matching nothing, because the
     * failure mode of a scope that silently no-ops is a screen that looks
     * merely empty while a bug hands someone a query with no filter at all.
     */
    public function scopeForEmployee($query, $employeeId)
    {
        $id = (int) $employeeId;

        if ($id <= 0) {
            throw new \InvalidArgumentException(
                'forEmployee() needs a real employee id; got: '.var_export($employeeId, true)
            );
        }

        return $query->where('barber_id', $id);
    }

    public const STATUS_CANCELLED = 'cancelled';

    /**
     * The value behind the unique index that stops one barber being booked
     * twice at the same time. Written only by BookingObserver, and absent
     * from $fillable so no request can set it.
     *
     * NULL means "this row claims no barber's slot", which covers both a
     * cancelled booking and an "any available barber" one. NULLs do not
     * collide in a unique index, so a cancellation frees the slot at once and
     * unassigned bookings are limited by the shop's capacity instead.
     *
     * Date and time are normalised before being joined. Without that, '10:00'
     * and '10:00:00' are the same appointment but two different keys, and the
     * constraint would wave both through.
     */
    public function resolveSlotKey(): ?string
    {
        if ($this->barber_id === null || $this->status === self::STATUS_CANCELLED) {
            return null;
        }

        if (empty($this->date) || empty($this->time)) {
            return null;
        }

        return sprintf(
            '%s|%s|%d',
            Carbon::parse($this->date)->toDateString(),
            Carbon::parse($this->time)->format('H:i:s'),
            $this->barber_id
        );
    }
}
