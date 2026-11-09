<?php

namespace App\Models;

use App\Observers\EmployeeObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

#[ObservedBy(EmployeeObserver::class)]
class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'avatar',
        'is_active',
        'sort_order',
        'commission_rate',
        'public_profile',
        'photo_path',
        'title',
        'bio',
        'instagram',
        'specialties',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'commission_rate' => 'decimal:2',
        'public_profile' => 'boolean',
        'specialties' => 'array',
    ];

    public function posOrders()
    {
        return $this->hasMany(PosOrder::class);
    }

    public function payments()
    {
        return $this->hasMany(EmployeePayment::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'barber_id');
    }

    /** The marketing services this barber is known for. Display only. */
    public function specialtyServices()
    {
        return Service::whereIn('id', $this->specialties ?? [])
            ->orderBy('name')
            ->get();
    }

    /** First name, for "Book with Omar" style buttons. */
    public function getFirstNameAttribute(): string
    {
        return Str::before(trim($this->name), ' ') ?: $this->name;
    }

    /** Commission earned on a subtotal amount (in currency, not %). */
    public function commissionOn(float $subtotal): float
    {
        return round($subtotal * (float) $this->commission_rate / 100, 2);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    /**
     * The ONLY barbers that may ever be shown publicly or offered for booking.
     *
     * Publishing and employment are separate facts: an employee can be active
     * in the POS without appearing on the website, and un-publishing must not
     * deactivate someone who is still taking sales. Both flags are therefore
     * required, and every public read path goes through here so neither can be
     * forgotten at a call site.
     */
    public function scopePublicBarbers($q)
    {
        return $q->where('public_profile', true)->where('is_active', true);
    }

    public const PUBLIC_BARBERS_CACHE_KEY = 'barbers.public';
    public const PUBLIC_BARBERS_CACHE_TTL = 600; // 10 minutes

    /**
     * The published barbers, for the booking modal in the footer.
     *
     * Cached and defended, because a View::composer('*') runs this on EVERY
     * page — which makes the whole site only as available as this one query.
     * On 8 Nov 2026 it took the site down: the deploy pulled code that filters
     * on `public_profile` minutes before the migration that adds the column,
     * and every request 500'd in the gap.
     *
     * So a failure here returns an empty collection and logs. The modal then
     * offers "any available barber" and nothing else, which is the behaviour
     * the site had before barbers existed — a degraded booking form beats no
     * site at all. Deploy order is still the real fix; this is the seatbelt.
     *
     * Catches Throwable rather than QueryException on purpose: a missing
     * column, an unreachable database and an unwritable cache store all have
     * the same correct answer, and the point is that nothing escapes.
     */
    public static function publicBarbersCached(): Collection
    {
        try {
            return Cache::remember(
                self::PUBLIC_BARBERS_CACHE_KEY,
                self::PUBLIC_BARBERS_CACHE_TTL,
                fn () => static::publicBarbers()
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name', 'slug'])
            );
        } catch (\Throwable $e) {
            Log::warning('Could not load public barbers; serving an empty list', [
                'err' => $e->getMessage(),
            ]);

            return collect();
        }
    }

    /** Called by EmployeeObserver whenever an employee is saved or deleted. */
    public static function forgetPublicBarbersCache(): void
    {
        try {
            Cache::forget(self::PUBLIC_BARBERS_CACHE_KEY);
        } catch (\Throwable $e) {
            // A cache store that cannot forget will expire the entry within
            // the TTL anyway. Never let this break saving an employee.
            Log::warning('Could not clear the public barbers cache', [
                'err' => $e->getMessage(),
            ]);
        }
    }

    public function scopeSearch($query, $request)
    {
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('name', 'LIKE', $search);
        }
        return $query;
    }
}
