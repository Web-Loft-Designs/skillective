<p>New Booking Request</p>
<p>Please approve within the next [[time_to_approve_booking]] hours</p>
<ul>
    <li>Booking ID: [[id]]</li>
    <li>Price: $[[spot_price]]</li>
    <li>Special Request: [[special_request]]</li>
    <li>Client: [[student_name]]</li>
    <li>Instructor: [[instructor_name]]</li>
    <li>Lesson: [[lesson_start]]-[[lesson_end]], [[lesson_location]]</li>
    <li>Genre: [[lesson_genre]]</li>
</ul>
<p>Click <a href="{{ config('app.url') }}/instructor/bookings?page=1&type=pending" target="_blank">HERE</a> to approve this lesson!</p>