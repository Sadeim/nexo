<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    public function scopeSearch($query, $request)
    {
        if (!empty($request->search['value'])) {
            $search = '%' . $request->search['value'] . '%';
            return $query->where('name', 'LIKE', $search);
        }
        return $query;
    }
}
