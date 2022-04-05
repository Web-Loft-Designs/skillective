<p>Your Client has requested the following lesson to be cancelled.  Click <a href="{{ config('app.url') }}/instructor/bookings?page=1&type=pending_cancellation" target="_blank">HERE</a> to approve this cancellation</p>
<ul>
    <li>Booking ID: [[id]]</li>
    <li>Price: $[[spot_price]]</li>
    <li>Special Request: [[special_request]]</li>
    <li>Client: [[student_name]]</li>
    <li>Instructor: [[instructor_name]]</li>
    <li>Lesson: [[lesson_start]]-[[lesson_end]], [[lesson_location]]</li>
    <li>Genre: [[lesson_genre]]</li>
</ul>
{{--<p><a href="[[booking_url]]" target="_blank">View booking</a> | <a href="[[lesson_url]]" target="_blank">View lesson</a></p>--}}