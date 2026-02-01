<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\AdminNotification;
use App\Models\About;
use App\Models\Achievement;
use App\Models\Blog;
use App\Models\Booking;
use App\Models\Category;
use App\Models\Client;
use App\Models\Consultation;
use App\Models\Contact;
use App\Models\Event;
use App\Models\Faq;
use App\Models\Feature;
use App\Models\How;
use App\Models\Instagram;
use App\Models\Newsletter;

use App\Models\Section;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Slider;

use App\Models\UserMessages;
use App\Models\Work;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function home()
    {
        $data['slider'] = Slider::first();
        $data['about'] = About::first();
        $data['works'] = Work::where('is_featured',1)->get();
        $data['services'] = Service::where('is_featured', 1)->get();

        $data['sections'] = Section::whereIn('key', [
            'services_section',
            'contact_page',
        ])->get()->keyBy('key');

        return view('frontend.home', $data);
    }

    public function services()
    {
        $data['services'] = Service::get();

        $data['works'] = Work::where('is_featured',1)->get();

        $data['sections'] = Section::whereIn('key', [
            'services_section',
        ])->get()->keyBy('key');

        return view('frontend.services', $data);
    }

    public function gallery()
    {
        $data['works'] = Work::where('is_featured', 0)->get();

        return view('frontend.gallery',$data);
    }

    // Get all active services for the booking form
    public function getServices()
    {
        $services = Service::get(['id', 'name']);
        return response()->json(['services' => $services]);
    }

    // Store booking
    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'service_id' => 'required|exists:services,id',
            'time' => 'required|string',
        ]);
        $validated['persons'] = 1;
        $validated['status'] = 'pending';

        Booking::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Booking saved successfully.'
        ], 200);
    }

    public function contactUs()
    {
        $data['sections'] = Section::whereIn('key', [
            'contact_page',

        ])->get()->keyBy('key');


        return view('frontend.contact', $data);
    }


    public function contactStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400);
        }

        $dataR = $request->only(['name', 'email', 'subject', 'message']);
        $message = UserMessages::create($dataR);
        $email = Setting::where('key', 'email')->first()->value;
        Mail::to($email)->send(new AdminNotification($message, 'contact'));

        return response()->json([
            'success' => true,
            'message' => 'Done Successfully'
        ], 200);
    }
}
