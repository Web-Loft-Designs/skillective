<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class StudentLibraryController extends Controller
{

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $vars = [
            'page_title'	=> 'My Library'
        ];

        return view('frontend.student.library', $vars);
    }

}
