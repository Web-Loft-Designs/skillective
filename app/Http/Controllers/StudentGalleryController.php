<?php

namespace App\Http\Controllers;



use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class StudentGalleryController extends Controller
{

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $vars = [
            'page_title'	=> 'Gallery',
			'userMedia'	=> Auth::user()->getGalleryMedia()
        ];

        return view('frontend.student.gallery', $vars);
    }

}
