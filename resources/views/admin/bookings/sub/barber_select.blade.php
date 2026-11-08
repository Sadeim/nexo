{{--
    Inline barber assignment for one booking row.

    "Unassigned" is a real choice, not a placeholder: it means "any available
    barber", which is what the customer asked for when they did not pick one.
    Only published barbers are offered — assigning an unpublished employee
    would fill a chair no customer was ever shown.
--}}
<select class="form-select form-select-sm js-assign-barber"
        style="min-width:150px"
        data-url="{{ route('admin.bookings.assign_barber', $booking->id) }}"
        data-current="{{ $booking->barber_id ?? '' }}"
        aria-label="Assign barber">
    <option value="" {{ $booking->barber_id === null ? 'selected' : '' }}>Unassigned</option>
    @foreach ($barbers as $barber)
        <option value="{{ $barber->id }}" {{ (int) $booking->barber_id === (int) $barber->id ? 'selected' : '' }}>
            {{ $barber->name }}
        </option>
    @endforeach
</select>
