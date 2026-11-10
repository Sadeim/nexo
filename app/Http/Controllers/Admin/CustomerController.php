<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\CustomerResource;
use App\Models\Customer;
use App\Support\Phone;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * The customer list, and the export that makes it worth having.
 *
 * Read-only. Nothing here sends a message, and nothing here grants consent —
 * consent is given by the customer at the booking form and is only ever
 * recorded, never conferred from this side.
 *
 * Gated on view_bookings rather than a permission of its own: customers are
 * what bookings are made of, the permission already exists on this
 * installation, and inventing a new one would mean a menu item that is
 * invisible until somebody remembers to seed it.
 */
class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_bookings');
    }

    public function index()
    {
        return view('admin.customers.index', [
            'total' => Customer::count(),
            'consented' => Customer::consented()->count(),
        ]);
    }

    public function datatable(Request $request)
    {
        $items = Customer::query()
            ->orderByDesc('last_booked_at')
            ->orderByDesc('id')
            ->search($request);

        return $this->filterDataTable($items, $request, null, CustomerResource::class);
    }

    public function show($id)
    {
        $customer = Customer::findOrFail($id);

        return view('admin.customers.show', [
            'customer' => $customer,
            'bookings' => $customer->bookings()
                ->with(['service', 'barber'])
                ->orderByDesc('date')
                ->orderByDesc('time')
                ->paginate(25),
        ]);
    }

    /**
     * The consented numbers, as a CSV.
     *
     * ONLY the consented ones. This file is the list that gets messaged, so
     * the filter is the whole point of it: a number in this file is a number
     * somebody ticked a box for, and there is no option, flag or query
     * parameter here that widens it. The timestamp and the source travel with
     * each row so the evidence stays attached to the number.
     *
     * Streamed rather than built in memory — it is a list that only grows.
     */
    public function export(): StreamedResponse
    {
        $filename = 'nexo-sms-consented-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'phone', 'first_name', 'last_name', 'email',
                'consent_given_at', 'consent_source', 'bookings', 'last_booked_at',
            ]);

            Customer::consented()
                ->orderBy('id')
                ->chunk(500, function ($chunk) use ($out) {
                    foreach ($chunk as $customer) {
                        fputcsv($out, [
                            // E.164, which is what a messaging provider wants.
                            Phone::e164($customer->phone),
                            $customer->first_name,
                            $customer->last_name,
                            $customer->email,
                            optional($customer->sms_consent_at)->toIso8601String(),
                            $customer->consent_source,
                            $customer->bookings_count,
                            optional($customer->last_booked_at)->toIso8601String(),
                        ]);
                    }
                });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }
}
