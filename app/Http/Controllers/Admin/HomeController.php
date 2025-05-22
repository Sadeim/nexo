<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Client;
use App\Models\Newsletter;
use App\Models\Section;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\Work;
use DateTime;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view_home', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $data['messages'] = UserMessages::latest()->take(5)->get();
        // $data['subscribers'] = Newsletter::latest()->get();
        // $data['servicesCount'] = Service::count();
        // $data['testimonialsCount'] = Testimonial::count();
    
        $data = [
            'clientsCount' => Client::count(),
            'servicesCount' => Service::count(),
            'worksCount' => Work::count(),
            'blogsCount' => Blog::count(),
            'testimonialsCount' => Testimonial::count(),
            
            'worksByTypeLabels' => Work::groupBy('category')->pluck('category'),
            'worksByTypeData' => Work::selectRaw('category, count(*) as count')
                ->groupBy('category')
                ->get()
                ->map(function($item) {
                    return [
                        'value' => $item->count,
                        'name' => $item->category
                    ];
                }),
                
            'projectsMonthly' => Work::selectRaw('MONTH(created_at) as month, count(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count'),
                
            'clientsMonthly' => Client::selectRaw('MONTH(created_at) as month, count(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count'),
                
            'blogsMonthly' => Blog::selectRaw('MONTH(created_at) as month, count(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count'),
                
            'recentClients' => Client::latest()->take(5)->get(),
            
            'newSubscribersThisMonth' => Newsletter::whereMonth('created_at', date('m'))->count(),
            'totalSubscribers' => Newsletter::count(),

        ];

        $data['newsletterStats'] = Newsletter::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')->orderBy('month')->get();
            
        $months = [];
        $subscribers = [];
        
        foreach ($data['newsletterStats'] as $stat) {
            $months[] = DateTime::createFromFormat('!m', $stat->month)->format('F');
            $subscribers[] = $stat->count;
        }
        
        $data['newsletterChartData'] = [
            'months' => $months,
            'subscribers' => $subscribers
        ];

        return view('admin.home.index', $data);
    }

    public function aboutPage()
    {
        $data['section'] = Section::where('key', 'about_page')->first();
        return view('admin.home.about_page', $data);
    }

    public function blogPage()
    {
        $data['section'] = Section::where('key', 'blog_page')->first();
        return view('admin.home.blog_page', $data);
    }

    public function contactPage()
    {
        $data['section'] = Section::where('key', 'contact_page')->first();
        return view('admin.home.contact_page', $data);
    }
}
