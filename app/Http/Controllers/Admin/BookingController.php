<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\SaveImageTrait;

class BookingController extends Controller
{
    use SaveImageTrait;

    public function __construct()
    {
        $this->middleware('permission:view_bookings|add_bookings', ['only' => ['index','store']]);
        $this->middleware('permission:add_bookings', ['only' => ['create','store']]);
        $this->middleware('permission:edit_bookings', ['only' => ['edit','update']]);
        $this->middleware('permission:delete_bookings', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('admin.bookings.index');
    }

    public function datatable(Request $request) 
    {
        $items = Booking::query()->orderBy('id', 'DESC');
        return $this->filterDataTable($items, $request);
    }

    public function create()
    {
        // return view('admin.bookings.create');
    }

    public function store(CreateBookingRequest $request)
    {

    }

    public function edit($id)
    {
        $booking = Booking::findOrFail($id);
        return view('admin.bookings.create', compact('booking'));
    }

    public function update(CreateBookingRequest $request, $id)
    {

    }

    public function destroy($id)
    {
        Booking::destroy($id);
        return $this->response_api(200, __('admin.form.deleted_successfully'), '');
    }
}
