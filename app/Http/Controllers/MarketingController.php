<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class MarketingController extends Controller
{
    /**
     * StudAI Hire — India's first complete autonomous AI hiring platform.
     * Every marketing page is content-driven from config/* files and rendered
     * through the shared cinematic layout (layouts.site).
     */

    public function home(): View
    {
        return view('pages.landing', [
            'page' => config('landing'),
        ]);
    }

    public function features(): View
    {
        return view('pages.marketing.features', [
            'seo' => [
                'title'       => 'Features — One OS for your entire career | StudAI Hire',
                'description' => 'Explore the StudAI Hire platform: autonomous applying, smart job search, resume studio, interview AI, negotiation coach and S.C.O.U.T. for employers.',
                'keywords'    => 'AI hiring platform features, autonomous job agent, AI career tools India',
            ],
        ]);
    }

    public function howItWorks(): View
    {
        return view('pages.marketing.how-it-works', [
            'seo' => [
                'title'       => 'How It Works — Set it once, then arrive | StudAI Hire',
                'description' => 'See how StudAI Hire turns the job hunt into a goal you set and an AI agent that delivers — searching, applying, preparing and negotiating for you.',
                'keywords'    => 'how autonomous job search works, AI job agent process India',
            ],
        ]);
    }

    public function pricing(): View
    {
        $meta = config('pricing.meta');

        return view('pages.marketing.pricing', [
            'seo' => [
                'title'       => $meta['meta_title'],
                'description' => $meta['meta_desc'],
                'keywords'    => $meta['keywords'],
            ],
        ]);
    }

    public function about(): View
    {
        return view('pages.marketing.about', [
            'seo' => [
                'title'       => 'About — Careers should run on autopilot | StudAI Hire',
                'description' => 'StudAI Hire is India’s first complete autonomous AI hiring platform, built to give every ambition an agent that works for them.',
                'keywords'    => 'about StudAI Hire, autonomous hiring India, AI career platform',
            ],
        ]);
    }

    public function contact(): View
    {
        return view('pages.marketing.contact', [
            'seo' => [
                'title'       => 'Contact — Let’s talk | StudAI Hire',
                'description' => 'Get in touch with the StudAI Hire team for questions, partnerships or press.',
                'keywords'    => 'contact StudAI Hire, support, partnerships',
            ],
        ]);
    }

    public function faq(): View
    {
        $meta = config('faq.meta');

        return view('pages.marketing.faq', [
            'seo' => [
                'title'       => $meta['meta_title'],
                'description' => $meta['meta_desc'],
                'keywords'    => $meta['keywords'],
            ],
        ]);
    }

    public function careers(): View
    {
        return view('pages.marketing.careers', [
            'seo' => [
                'title'       => 'Careers — Build the future of hiring | StudAI Hire',
                'description' => 'Join StudAI Hire and help build India’s first complete autonomous AI hiring platform.',
                'keywords'    => 'StudAI Hire careers, AI jobs India, work at StudAI Hire',
            ],
        ]);
    }

    public function forEmployers(): View
    {
        return view('pages.marketing.employers', [
            'seo' => [
                'title'       => 'For Employers — Hire on autopilot with S.C.O.U.T. | StudAI Hire',
                'description' => 'S.C.O.U.T. is StudAI Hire’s autonomous ATS. Screen, rank and shortlist candidates automatically and hire the best people faster.',
                'keywords'    => 'AI hiring platform employers, autonomous ATS India, recruitment automation',
            ],
        ]);
    }

    public function product(string $slug): View
    {
        $product = config("products.$slug");
        abort_if($product === null, 404);

        return view('pages.marketing.product', [
            'slug'    => $slug,
            'product' => $product,
            'seo'     => [
                'title'       => $product['meta_title'],
                'description' => $product['meta_desc'],
                'keywords'    => $product['keywords'],
                'og_type'     => 'product',
            ],
        ]);
    }

    public function useCases(): View
    {
        return view('pages.marketing.usecases-index', [
            'seo' => [
                'title'       => 'Use Cases — Built for every career moment | StudAI Hire',
                'description' => 'See how StudAI Hire helps students, freshers, working professionals, career switchers, returners and employers.',
                'keywords'    => 'AI job search use cases, career help India, hiring solutions',
            ],
        ]);
    }

    public function useCase(string $slug): View
    {
        $usecase = config("usecases.$slug");
        abort_if($usecase === null, 404);

        return view('pages.marketing.usecase', [
            'slug'    => $slug,
            'usecase' => $usecase,
            'seo'     => [
                'title'       => $usecase['meta_title'],
                'description' => $usecase['meta_desc'],
                'keywords'    => $usecase['keywords'],
            ],
        ]);
    }

    public function blog(): View
    {
        $meta = config('blog.meta');

        return view('pages.marketing.blog-index', [
            'seo' => [
                'title'       => $meta['meta_title'],
                'description' => $meta['meta_desc'],
                'keywords'    => $meta['keywords'],
            ],
        ]);
    }

    public function blogShow(string $slug): View
    {
        $post = config("blog.posts.$slug");
        abort_if($post === null, 404);

        return view('pages.marketing.blog-post', [
            'slug' => $slug,
            'post' => $post,
            'seo'  => [
                'title'       => $post['meta_title'],
                'description' => $post['meta_desc'],
                'keywords'    => $post['keywords'],
                'og_type'     => 'article',
            ],
        ]);
    }

    public function legal(string $slug): View
    {
        $doc = config("legal.$slug");
        abort_if($doc === null, 404);

        return view('pages.marketing.legal', [
            'slug' => $slug,
            'doc'  => $doc,
            'seo'  => [
                'title'       => $doc['meta_title'],
                'description' => $doc['meta_desc'],
            ],
        ]);
    }

    /** Canonical short routes that resolve to legal documents. */
    public function privacy(): View
    {
        return $this->legal('privacy');
    }

    public function terms(): View
    {
        return $this->legal('terms');
    }

    public function refundPolicy(): View
    {
        return $this->legal('refund');
    }

    public function cookiePolicy(): View
    {
        return $this->legal('cookie');
    }

    public function security(): View
    {
        return $this->legal('security');
    }
}
