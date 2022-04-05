<?php

namespace App\Http\Controllers;

class StudentLibraryVideoController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $vars = [
            'page_title'	=> 'Video'
        ];

        return view('frontend.student.video', $vars);
    }

}
