<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\AdminNotification;
use App\Models\About;
use App\Models\Achievement;
use App\Models\Faq;
use App\Models\Instagram;
use App\Models\Skill;
use App\Models\Team;
use App\Models\Testimonial;
use App\Models\UserMessages;
use App\Models\Work;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function contactUs() 
    {
        $data['instagrams'] = Instagram::get();
        return view('frontend.contact_us', $data);
    }

    public function contactStore(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'number' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return $this->response_api(400, $validator->errors()->first(), '');
        }

        $dataR = $request->only(['name','email','subject','message', 'number']);
        $message = UserMessages::create($dataR);
        
        // Mail::to(config('mail.admin_email'))->send(new AdminNotification($message, 'contact'));

        return $this->response_api(200, 'Done Successfully', '');
    }

    public function aboutUs() 
    {
        $data['about'] = About::first();
        $data['skills'] = Skill::get();
        $data['works'] = Work::get();
        $data['testimonials'] = Testimonial::get();
        $data['faqs'] = Faq::get(); 
        $data['achievements'] = Achievement::get();
        $data['teams'] = Team::get();
        return view('frontend.about_us', $data);
    }
}
