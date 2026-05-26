<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MarketingController extends Controller
{
    /**
     * StudAI Hire - India's First Autonomous Career OS
     * All marketing pages updated with StudAI Hire branding
     */
    
    public function home()
    {
        // $testimonials = Cache::remember('marketing.home.testimonials', 3600, function () {
        //     return Testimonial::active()->take(3)->get();
        // });

        return view('pages.landing');
    }

    public function features()
    {
        return view('pages.features-studai-path');
    }

    public function pricing()
    {
        // $plans = SubscriptionPlan::where('is_active', true)
        //     ->orderBy('sort_order')
        //     ->get()
        //     ->groupBy('billing_period');
        
        // $monthlyPlans = $plans->get('monthly', collect());
        // $yearlyPlans = $plans->get('yearly', collect());
        
        return view('pages.pricing-studai-path');
    }

    public function about()
    {
        return view('pages.about-studai-path');
    }

    public function contact()
    {
        return view('pages.contact-studai-path');
    }

    public function howItWorks()
    {
        return view('pages.how-it-works-studai-path');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function blog()
    {
        return view('pages.blog-studai-path');
    }

    public function refundPolicy()
    {
        return view('pages.refund-policy-studai-path');
    }

    public function cookiePolicy()
    {
        return view('pages.cookie-policy');
    }

    public function security()
    {
        return view('pages.security');
    }

    public function forEmployers()
    {
        return view('pages.employers-studai-path');
    }
}
