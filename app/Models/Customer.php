<?php

namespace App\Models;

use App\Support\Phone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Somebody who has given the shop their phone number.
 *
 * Not an account: there is no password and nothing to log in to. It is the
 * record that outlives a single appointment, and — where consent was given —
 * the list the shop may text.
 */
class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'first_name',
        'last_name',
        'email',
        // Consent is deliberately absent. It is only ever written through
        // CustomerService, which stamps the time and the source with it; a
        // mass-assigned true with no evidence behind it is exactly the thing
        // this column exists to prevent.
    ];

    protected $casts = [
        'sms_marketing_consent' => 'boolean',
        'sms_consent_at' => 'datetime',
        'last_booked_at' => 'datetime',
        'bookings_count' => 'integer',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /** "Omar Haddad", or just the number when nobody has given a name yet. */
    public function getNameAttribute(): string
    {
        $name = trim(($this->first_name ?? '').' '.($this->last_name ?? ''));

        return $name !== '' ? $name : Phone::pretty($this->phone);
    }

    public function getPrettyPhoneAttribute(): string
    {
        return Phone::pretty($this->phone);
    }

    /** The only customers the shop may send marketing to. */
    public function scopeConsented($query)
    {
        return $query->where('sms_marketing_consent', true);
    }

    /**
     * Phone or name, for the dashboard's one search box.
     *
     * The phone is matched on digits, so searching "(224) 669" finds a number
     * stored as 2246698063 — the owner types what is in their contacts, not
     * what is in the column.
     */
    public function scopeSearch($query, $request)
    {
        $term = trim((string) ($request->search['value'] ?? ''));

        if ($term === '') {
            return $query;
        }

        $digits = preg_replace('/\D+/', '', $term);

        return $query->where(function ($q) use ($term, $digits) {
            $q->where('first_name', 'LIKE', '%'.$term.'%')
                ->orWhere('last_name', 'LIKE', '%'.$term.'%')
                ->orWhere('email', 'LIKE', '%'.$term.'%');

            if ($digits !== '') {
                $q->orWhere('phone', 'LIKE', '%'.$digits.'%');
            }
        });
    }
}
