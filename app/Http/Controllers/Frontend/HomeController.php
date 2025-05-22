<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\AdminNotification;
use App\Models\About;
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
use App\Models\Reason;
use App\Models\ReasonTab;
use App\Models\Section;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\Team;
use App\Models\Testimonial;
use App\Models\UserMessages;
use App\Models\Work;
use App\Models\Approach;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function home()
    {
        $data['slider'] = Slider::first();
        $data['about'] = About::first();
        $data['services'] = Service::get();
        $data['works'] = Work::get();
        $data['teams'] = Team::get();
        $data['testimonials'] = Testimonial::get();
        $data['faqs'] = Faq::get();
        $data['blogs'] = Blog::get();
        $data['clients'] = Client::get();
        $data['sections'] = Section::whereIn('key', [
            'services_section',
            'testimonials_section',
            'works_section',
            'about_section',
            'teams_section',
            'faqs_section',
            'sliders_section',
            'clients_section',
            'blog_section',
        ])->get()->keyBy('key');
        $data['how'] = How::first();
        
        return view('frontend.home', $data);
    }

    public function storeConsultation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'required|string|max:20',
            'service' => 'required', // تأكد من اختيار خدمة صحيحة
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $message = Consultation::create($request->all());

        Mail::to(config('mail.admin_email'))->send(new AdminNotification($message, 'consultation'));

        return response()->json([
            'message' => 'Your consultation request has been received successfully!'
        ]);
    }

    public function storeNewsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletters,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()->first()], 422);
        }

        Newsletter::create([
            'email' => $request->email,
        ]);

        return response()->json(['status' => 'success', 'message' => 'Thanks for subscribing!']);
    }

    public function storeBooking(Request $request)
    {
        $validated = $request->validate([
            'persons' => 'required|integer|min:1|max:6',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        Booking::create($validated);

        return response()->json(['message' => 'Booking saved successfully.']);
    }

    public function contactUs()
    {
        $data['instagrams'] = Instagram::get();
        $data['about'] = About::first();
        $data['section'] = Section::where('key', 'contact_section')->first();
        return view('frontend.contact_us', $data);
    }

    public function contactStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required'],
            'email' => ['required', 'string', 'email', 'max:255'],
            // 'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            // 'number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->response_api(400, $validator->errors()->first(), '');
        }

        $dataR = $request->only(['name', 'phone', 'email','subject','message']);
        $message = UserMessages::create($dataR);
        $email = Setting::where('key', 'email')->first()->value;
         Mail::to($email)->send(new AdminNotification($message, 'contact'));

        return $this->response_api(200, 'Done Successfully');
    }

    public function aboutUs()
    {
        $data['about'] = About::with(['openingHours' => function ($query) {
                            $query->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')");
                        }])->first();
                        
        $data['testimonials'] = Testimonial::get();
        $data['teams'] = Team::get();
        $data['clients'] = Client::get();
        $data['skills'] = Skill::first();
        $data['reasons'] = Reason::get();
        $data['how'] = How::first();
        $data['approach'] = Approach::first();
        $data['sections'] = Section::whereIn('key', [
            'about_page',
            'skills_section',
            'approaches_section',
            'reasons_section',
        ])->get()->keyBy('key');
        
        return view('frontend.about_us', $data);
    }

    public function projects()
    {
        $data['works'] = Work::get();
        $data['categories'] = Category::get();
        $data['sections'] = Section::whereIn('key', [
            'work_page',
            'projects_section',
        ])->get()->keyBy('key');
        return view('frontend.projects', $data);
    }
}
