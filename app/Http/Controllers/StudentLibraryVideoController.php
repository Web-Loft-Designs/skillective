<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class StudentLibraryVideoController extends Controller
{

    /**
     * @return Application|Factory|View
     */
    public function index()
    {

        $vars = [
            'page_title'	=> 'Video'
        ];

        return view('frontend.student.video', $vars);
    }

}
