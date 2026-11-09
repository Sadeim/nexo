<?php

namespace App\Models;

use App\Observers\EmployeeObserver;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * A barber.
 *
 * Also the authenticatable for the `employee` guard behind the /me portal.
 * It stays an ordinary Model rather than extending Laravel's User, because
 * everything else about an employee — the POS label, the public profile, the
 * commission rate — predates the portal and must not change shape for it.
 */
#[ObservedBy(EmployeeObserver::class)]
class Employee extends Model implements AuthenticatableContract
{
    use AuthenticatableTrait;
    use HasFactory;

    /**
     * There is no remember_token column and no "remember me" on the portal: a
     * barber's phone is shared-ish and a long-lived cookie on it is a liability.
     *
     * Overridden as a method, not as the trait's $rememberTokenName property —
     * redeclaring a trait's property with a different value is a fatal
     * composition error in PHP. An empty name is how Laravel's own
     * Authenticatable trait is told there is no such column.
     */
    public function getRememberTokenName(): string
    {
        return '';
    }

    protected $fillable = [
        'name',
        'slug',
        // Settable by the owner in the dashboard. `password` is deliberately
        // absent: it is only ever written through setPortalPassword().
        'phone',
        'portal_role',
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
        'last_login_at' => 'datetime',
        'must_change_password' => 'boolean',
        'password' => 'hashed',
    ];

    /** Never serialise the hash, including into a view or a JSON response. */
    protected $hidden = [
        'password',
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

    // ---------- portal access ----------

    /** Their own day, and nothing else. */
    public const ROLE_BARBER = 'barber';

    /** The whole shop's day. The owner reads the portal as one of these. */
    public const ROLE_MANAGER = 'manager';

    /** The roles the dashboard offers, keyed by the stored value. */
    public const PORTAL_ROLES = [
        self::ROLE_BARBER => 'Barber',
        self::ROLE_MANAGER => 'Manager',
    ];

    /**
     * Whether the portal shows this employee the whole shop.
     *
     * Compared against the constant rather than tested for truthiness, so an
     * unloaded attribute, a null left by some other write path or a value
     * nobody recognises all answer "no". Widening what someone can see must
     * take an explicit, exact value in the column.
     */
    public function isManager(): bool
    {
        return $this->portal_role === self::ROLE_MANAGER;
    }

    /**
     * Phone numbers reduced to their digits, for storage and for lookup.
     *
     * The owner types whatever is in their contacts — "(224) 669-8063",
     * "+1 224 669 8063" — and the barber, months later, types something else.
     * Both land on the same stored value, so login does not fail on
     * punctuation.
     *
     * A US country code is dropped too: eleven digits beginning with 1 is
     * "+1" in front of a ten-digit number, so 12246698063 and 2246698063 are
     * the same phone. The length check is what keeps this safe — it only ever
     * fires on 11 digits, so a genuine ten-digit number starting with 1
     * (which the NANP does not issue as an area code anyway) is untouched,
     * and so is any other country's number of a different length.
     */
    public static function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);

        if (strlen($digits) === 11 && str_starts_with($digits, '1')) {
            $digits = substr($digits, 1);
        }

        return $digits === '' ? null : $digits;
    }

    /**
     * Whether this barber can sign in to /me at all.
     *
     * Both halves are required. A row with a phone and no password cannot
     * authenticate, and a row with a password and no phone cannot be found at
     * login — so a half-configured employee is closed, never ajar.
     */
    public function hasPortalAccess(): bool
    {
        return filled($this->phone) && filled($this->password);
    }

    /**
     * Set a password and require it to be changed on next sign-in.
     *
     * Every path that writes a password goes through here, so there is one
     * place where "a password was set by someone other than its owner" is
     * recorded. The owner typing a temporary password and a barber choosing
     * their own differ only in this flag, which is why changePasswordTo()
     * exists separately rather than taking a boolean.
     */
    public function setPortalPassword(string $plain): void
    {
        $this->forceFill([
            'password' => $plain,
            'must_change_password' => true,
        ])->save();
    }

    /** The barber choosing their own password, which clears the flag. */
    public function changePasswordTo(string $plain): void
    {
        $this->forceFill([
            'password' => $plain,
            'must_change_password' => false,
        ])->save();
    }

    public function recordLogin(): void
    {
        $this->forceFill(['last_login_at' => now()])->save();
    }

    /** Lookup for the portal login, on the exact stored phone. */
    public function scopeWithPhone($query, string $phone)
    {
        return $query->where('phone', $phone);
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
