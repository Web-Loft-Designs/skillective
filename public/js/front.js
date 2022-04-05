if (window.location.hash.search(/venmoSuccess=/)===-1){
	Cookies.remove('currentOrderDetails');
}

function hideUpcomingLessonNotification(lessonId){
	Cookies.set('hiddenUpcomingLessonId', lessonId);
	jQuery('#upcoming-lesson-notification').remove();
}

function hideUpcomingBookingNotification(bookingId){
	Cookies.set('hiddenUpcomingBookingId', bookingId);
	jQuery('#upcoming-booking-notification').remove();
}

$(document).ready(function() {
    $('.uploaded-faq-image').magnificPopup({type:'image'});

    $('.uploaded-faq-video').magnificPopup({
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false,
        iframe: {
            markup: '<div class="mfp-iframe-scaler">'+
            '<div class="mfp-close"></div>'+
            '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>'+
            '</div>',
            srcAction: 'iframe_src',
        }
    });
});