<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;


class TestSeeder extends Seeder
{
	public function run()
    {
        $setting = Setting::where('name', 'booking_confirmation_text')->first();
        $setting->update([
            'value' => '<h3>You\'re all set!</h3>


<p> For in-person or virtual lessons : </p>
<ul>
 <li>
 <p>  Details for the Lesson can now be found in the <a href="/student/bookings"> Bookings section </a> </p>
 </li>
 <li>
 <p> For In-Person Lessons: Please arrive ~15 minutes early to make sure you are ready to start on time </p>
 </li>
 <li>
 <p> For Virtual Lessons: You can join the lesson 5 minutes before your time starts in your  <a href="/student/bookings"> Bookings section </a> </p>
 </li>
</ul>
<p> For pre-recorded content : </p>
<ul>
 <li>
 <p> This content can be found in <a href="/student/library"> your library</a>. It is available now and can also be accessed at any time. </p>
 </li>
</ul>
<p>&nbsp;</p>'
        ]);

        echo "{$setting->name} update succes";

	}

}
