<?php

namespace App\Http\Controllers;

class StudentLibraryController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $vars = [
            'page_title'	=> 'My Library'
        ];

        return view('frontend.student.library', $vars);
    }

}
