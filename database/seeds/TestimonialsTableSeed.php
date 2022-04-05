<?php

use Illuminate\Database\Seeder;
use App\Models\Testimonial;
use Illuminate\Support\Facades\File;

class TestimonialsTableSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Throwable
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Testimonial::all()->each(function($t){
			$t->clearMediaCollection('testimonial');
		});
        DB::table('testimonials')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $testimonials = [
			['name'=>'John Doe', 'instagram_handle'=>'yogatrainer_sun', 'position' => 1, 'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'],
			['name'=>'John Doe', 'instagram_handle'=>'yogatrainer_sun', 'position' => 2, 'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'],
		];

        foreach ($testimonials as $t) {
			$testimonial = Testimonial::create($t);
			\File::copy(public_path('images/avatar-default.png'), public_path('images/avatar-default-copy.png'));
			$testimonial->addMedia(public_path('images/avatar-default-copy.png'))->toMediaCollection('testimonial');
        }
    }
}
