<?php

namespace App\Http\Controllers;

use Auth;
use Session;

class StudentGalleryController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
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
