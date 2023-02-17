<?php

namespace App\Http\Controllers;

use App\Repositories\FaqRepository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\Testimonial;

class PageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @param Request $request
     * @param FaqRepository $faqRepository
     * @return Application|Factory|View|never
     */
    public function index(Request $request, FaqRepository $faqRepository)
    {
        $page = getCurrentPage();
        if (!$page){
			session()->forget(['current_page', 'prev_page']);
            return abort(404);
        }
        $templateVars = ['page'=>$page];
        if ($page->template=='about-us' || $page->template=='landing'){
            $testimonials = Testimonial::orderBy('created_at', 'desc')->get();
            $templateVars['testimonials'] = $testimonials;
        }elseif ($page->template=='how-it-works') {
            $categorizedFaqs = $faqRepository->getCategorizedFaqs();
            $templateVars['categorizedFaqs'] = $categorizedFaqs;
        }

        return view('frontend.pages.page', $templateVars);
    }
}
