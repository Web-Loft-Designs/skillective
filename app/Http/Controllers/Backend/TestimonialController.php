<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use File;
use URL;
use Image;

class TestimonialController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request){
        $testimonial = new Testimonial();
        captureRoutePerPage($request);
        return view('backend.testimonials.list', [
            'items' => $testimonial->getList($request->input(), getRoutePerPage( $request )),
            'filterValues' => $request->except('l'),
        ]);
    }

    public function create(Request $request){
        return view('backend.testimonials.add', [
            'page_title' => "Create Testimonial",
        ]);
    }

    public function edit(Request $request, Testimonial $testimonial){
        return view('backend.testimonials.edit', [
            'testimonial' => $testimonial,
            'page_title' => "Edit Testimonial",
        ]);
    }

    /**
     * @param Request $request
     * @param Testimonial $testimonial
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Testimonial $testimonial ){
        $this->validate($request, Testimonial::$rules);


		$testimonial->uploadImage($request->file('image'));

        $testimonial->update($request->all());

        $request->session()->flash('alert-success', 'Item updated!');

        return redirect( route('backend.testimonials.edit', $testimonial->id));
    }

    public function store(Request $request){
        $this->validate($request, Testimonial::$rules);

		$testimonial = Testimonial::create($request->all());

		$testimonial->uploadImage($request->file('image'));

        if ($testimonial){
            $request->session()->flash('alert-success', 'Testimonial created!');
            return redirect(route('backend.testimonials.edit', $testimonial->id));
        }else{
            return redirect(route('backend.testimonials.create'))->withInput();
        }
    }

    /**
     * @param Request $request
     * @param Testimonial $testimonial
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy(Request $request, Testimonial $testimonial ){
        File::delete($testimonial->image);
        $testimonial->delete($testimonial->id);
        $request->session()->flash('alert-success', 'Item deleted!');
        return redirect( route('backend.testimonials.index'));
    }
}
