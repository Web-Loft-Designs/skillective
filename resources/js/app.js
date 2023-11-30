/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

if (typeof Object.assign != "function") {
    Object.assign = function(target) {
        "use strict";
        if (target == null) {
            throw new TypeError("Cannot convert undefined or null to object");
        }

        target = Object(target);
        for (var index = 1; index < arguments.length; index++) {
            var source = arguments[index];
            if (source != null) {
                for (var key in source) {
                    if (Object.prototype.hasOwnProperty.call(source, key)) {
                        target[key] = source[key];
                    }
                }
            }
        }
        return target;
    };
}
import Vue from 'vue'
import {Vuelidate} from 'vuelidate'

Vue.use(Vuelidate)

require("./bootstrap");
require("url-search-params-polyfill");

window.Vue = require("vue").default;

import Vuex from 'vuex';
Vue.use(Vuex);

import store from './store';
Vue.use(store);

import Datepicker from "vuejs-datepicker";
import DropdownDatepicker from "vue-dropdown-datepicker";
Vue.use(DropdownDatepicker);

import FullCalendar from "@fullcalendar/vue";

Vue.use(FullCalendar);


// Import component
import Loading from "vue-loading-overlay";
import Slick from "vue-slick";
// Import stylesheet
import "vue-loading-overlay/dist/vue-loading.css";
import VueSlider from "vue-slider-component";
import "vue-slider-component/theme/antd.css";
require("sharer.js");

// Init plugin
Vue.use(Loading);
Vue.use(Slick);

import moment from "vue-moment";
Vue.use(moment);

import MFP from "magnific-popup";
Vue.use(MFP);


import ToggleButton from "vue-js-toggle-button";

Vue.use(ToggleButton);

import VueHotelDatepicker from "@northwalker/vue-hotel-datepicker";

if (window.jQuery == undefined) {
    window.jQuery = require("jquery").default;
}

import Multiselect from "vue-multiselect";
Vue.component("multiselect", Multiselect);

import Sticky from "vue-sticky-directive";
Vue.use(Sticky);

import VueCountdown from "@chenfengyuan/vue-countdown";
Vue.component(VueCountdown.name, VueCountdown);

// import VueResource from 'vue-resource'
// Vue.use(VueResource)

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

// Vue.component('profile-media-gallery', require('./components/ProfileMediaGallery.vue').default);

// vue extensions

import ReadMore from 'vue-read-more';

Vue.use(ReadMore);

import VCalendar from 'v-calendar';
Vue.use(VCalendar, {
  componentPrefix: 'vc',
  masks: {
    weekdays: 'WWW',
  },
  locale: 'en-US',
});

import VScrollLock from 'v-scroll-lock';
Vue.use(VScrollLock);

// vue global components to use in .blade.php files

Vue.component("cart-icon", require("./components/cart/CartIcon/CartIcon.vue").default);
Vue.component("nav-links", require("./components/header/NavLinks/NavLinks.vue").default);
Vue.component("profile-menu", require("./components/header/ProfileMenu/ProfileMenu.vue").default);
Vue.component("nav-drop-down", require("./components/header/NavDropDown/NavDropDown.vue").default);
Vue.component("cart", require("./components/cart/Cart/Cart.vue").default);
Vue.component("checkout", require("./components/cart/Checkout/Checkout.vue").default);
Vue.component("home", require("./components/home/Home/Home.vue").default);
Vue.component("calendar-input", require("./components/home/CalendarInput/CalendarInput.vue").default);
Vue.component("upcoming-lessons-near-you", require("./components/home/UpcomingLessonsNearYou/UpcomingLessonsNearYou.vue").default);
Vue.component("new-header", require("./components/header/NewHeader/NewHeader.vue").default);
Vue.component("add-lesson-time-button", require("./components/header/AddLessonTimeButton/AddLessonTimeButton.vue").default);
Vue.component("instructor-video-lessons", require("./components/student/InstructorVideoLessons/InstructorVideoLessons.vue").default);
Vue.component("global-shop", require("./components/student/GlobalShop/GlobalShop.vue").default);
Vue.component("my-library", require("./components/student/MyLibrary/MyLibrary.vue").default);
Vue.component("my-library-player", require("./components/student/MyLibraryPlayer/MyLibraryPlayer.vue").default);
Vue.component("instructor-my-shop", require("./components/instructor/InstructorMyShop/InstructorMyShop.vue").default);
Vue.component("lessons", require("./components/lessons/Lessons/Lessons.vue").default);
Vue.component("content-viewer", require("./components/profile/ContentViewer/ContentViewer.vue").default);
Vue.component("discount-management", require("./components/discounts/DiscountManagement/DiscountManagement.vue").default);



import LessonSimpleSearchFormFields from "./components/LessonSimpleSearchFormFields.vue";
Vue.component("lesson-simple-search-fields", LessonSimpleSearchFormFields);

Vue.component("Logs", require("./components/video/Logs.vue").default);
Vue.component(
    "SelectMediaDevices",
    require("./components/video/SelectMediaDevices.vue").default
);
Vue.component("Room", require("./components/video/Room.vue").default);
Vue.component(
    "lesson-participant-room-controls",
    require("./components/LessonParticipantRoomControls.vue").default
);

Vue.component(
    "contact-us-form",
    require("./components/ContactUsForm.vue").default
);

Vue.component(
    "instructor-registration-form",
    require("./components/InstructorRegistrationForm.vue").default
);
Vue.component(
    "student-registration-form",
    require("./components/StudentRegistrationForm.vue").default
);
Vue.component(
    "user-finish-registration-form",
    require("./components/UserFinishRegistrationForm.vue").default
);
Vue.component(
    "front-login-form",
    require("./components/FrontendLoginForm.vue").default
);
Vue.component(
    "front-login-form-modal",
    require("./components/FrontendLoginFormModal.vue").default
);

Vue.component(
    "profile-simple-gallery",
    require("./components/ProfileSimpleGallery.vue").default
);
Vue.component(
    "dashboard-media-gallery",
    require("./components/DashboardMediaGallery.vue").default
);
Vue.component(
    "instructor-media-gallery",
    require("./components/InstructorMediaGallery.vue").default
);
Vue.component(
    "student-media-gallery",
    require("./components/StudentMediaGallery.vue").default
);

Vue.component(
    "lessons-search-results-list",
    require("./components/LessonsSearchResultsList.vue").default
);
Vue.component(
    "instructor-simple-search-fields",
    require("./components/InstructorSimpleSearchFormFields.vue").default
);
Vue.component(
    "instructors-search-results-list",
    require("./components/InstructorsSearchResultsList.vue").default
);
Vue.component(
    "lesson-booking-form",
    require("./components/LessonBookingForm.vue").default
);
Vue.component(
  "lesson-booking-form-pp",
  require("./components/LessonBookingFormPp.vue").default
);
Vue.component(
    "location-modal",
    require("./components/SearchLessonDetailsModal.vue").default
);
Vue.component(
    "lesson-details-modal",
    require("./components/LessonDetailsModal.vue").default
);
Vue.component(
    "lesson-details-modal-admin",
    require("./components/LessonDetailsModalAdmin.vue").default
);

Vue.component(
    "profile-data-update-form",
    require("./components/ProfileDataUpdateForm.vue").default
);
Vue.component(
    "profile-geo-locations-form",
    require("./components/ProfileGeoLocationsForm.vue").default
);
Vue.component(
    "profile-notifications-update-form",
    require("./components/ProfileNotificationsUpdateForm.vue").default
);
Vue.component(
    "profile-password-change",
    require("./components/ProfilePasswordChange.vue").default
);
Vue.component(
    "profile-payment-account",
    require("./components/ProfilePaymentAccount").default
);

Vue.component(
    "braintree-merchant-form",
    require("./components/BraintreeMerchantForm.vue").default
);
Vue.component(
    "profile-lessons-list",
    require("./components/ProfileLessonsList.vue").default
);
Vue.component(
    "profile-lesson-form",
    require("./components/ProfileLessonForm.vue").default
);
Vue.component(
    "profile-upcoming-locations",
    require("./components/ProfileUpcomingLocations.vue").default
);
Vue.component(
    "profile-upcoming-virtual-lessons",
    require("./components/ProfileUpcomingVirtualLessons.vue").default
);
Vue.component(
    "profile-avatar-upload",
    require("./components/ProfileAvatarUpload.vue").default
);
Vue.component(
    "profile-invitations-limit",
    require("./components/ProfileInvitationsLimit.vue").default
);

Vue.component(
    "request-lesson-form",
    require("./components/RequestLessonForm.vue").default
);

Vue.component(
    "instructor-clients-list",
    require("./components/InstructorClientsList.vue").default
);

import InstructorClientsDashboardList from "./components/InstructorClientsDashboardList.vue";

Vue.component(
    "instructor-clients-dashboard-list",
    InstructorClientsDashboardList
);

Vue.component(
    "instructor-clients-add-form",
    require("./components/InstructorClientsAddForm.vue").default
);
Vue.component(
    "instructor-bookings-list",
    require("./components/InstructorBookingsList.vue").default
);
import TaxIdReminderPopup from "./components/instructor/TaxIdPopups/TaxIdReminderPopup.vue";
Vue.component(
  "tax-id-reminder-popup",
  TaxIdReminderPopup
);
import InstructorDashboardBookingsClients from "./components/InstructorDashboardBookingsClients.vue";
Vue.component(
    "instructor-dashboard-bookings-clients",
    InstructorDashboardBookingsClients
);
Vue.component(
    "instructor-bookings-dashboard-list",
    require("./components/InstructorBookingsDashboardList.vue").default
);
Vue.component("incomes", require("./components/Incomes.vue").default);
Vue.component(
    "instructor-payouts-list",
    require("./components/InstructorPayoutsList").default
);

Vue.component(
    "student-bookings-list",
    require("./components/StudentBookingsList.vue").default
);
Vue.component(
    "student-bookings-dashboard-list",
    require("./components/StudentBookingsDashboardList.vue").default
);
Vue.component(
    "student-instructors-list",
    require("./components/StudentInstructorsList.vue").default
);
Vue.component(
    "student-instructors-dashboard-list",
    require("./components/StudentInstructorsDashboardList.vue").default
);
Vue.component(
    "favorite-instructor",
    require("./components/FavoriteInstructor.vue").default
); // star on profile page
Vue.component(
    "student-instructor-geo-notifications-toggle",
    require("./components/StudentInstructorGeoNotificationsToggle.vue").default
);
Vue.component(
    "student-instructor-virtual-lesson-notifications-toggle",
    require("./components/StudentInstructorVirtualLessonNotificationsToggle.vue")
        .default
);

Vue.component(
    "send-notification-form",
    require("./components/SendNotificationForm.vue").default
);
Vue.component(
    "client-invitation-form",
    require("./components/ClientInvitationForm.vue").default
);
Vue.component(
    "instructor-invitation-form",
    require("./components/InstructorInvitationForm.vue").default
);
Vue.component(
    "favorite-instructor-invitation-form",
    require("./components/FavoriteInstructorInvitationForm.vue").default
);

// external
Vue.component("datepicker", Datepicker);

Vue.component(
    "dashboard-booking-item",
    require("./components/external/DashboardBookingItem").default
);
Vue.component(
    "dashboard-booking-item-request",
    require("./components/external/DashboardBookingItemRequest").default
);
Vue.component(
    "dashboard-booking-item-countdown",
    require("./components/external/DashboardBookingItemCountdown").default
);

Vue.component("vue-hotel-datepicker", VueHotelDatepicker);
Vue.component("slick", Slick);
Vue.component("vue-slider", VueSlider);
Vue.component(
    "time-picker",
    require("./components/external/customTimeRange").default
);
Vue.component(
    "time-price",
    require("./components/external/customPriceRange").default
);
Vue.component(
    "google-map-single",
    require("./components/external/google-map-single").default
);
Vue.component(
    "instructors-google-map-single",
    require("./components/external/instructors-google-map-single").default
);
Vue.component(
    "google-map-multiple",
    require("./components/external/google-map-multiple").default
);
Vue.component(
    "colendar-small",
    require("./components/external/colendar-small").default
);
Vue.component(
    "colendar-add-lesson",
    require("./components/external/colendar-add-lesson").default
);
Vue.component(
    "calendar-booked-lessons",
    require("./components/external/colendar-student").default
);
Vue.component(
    "chart-dashboard",
    require("./components/external/chart-dashboard").default
);
Vue.component(
    "chart-dashboard-2",
    require("./components/external/chart-dashboard-2").default
);
Vue.component(
    "chart-admin-dashboard",
    require("./components/external/chart-admin-dashboard").default
);

Vue.component(
    "panding-popup",
    require("./components/external/Panding-popup").default
);
Vue.component(
    "share-modal",
    require("./components/external/ShareSchedule").default
);
Vue.component(
    "set-goal-popup",
    require("./components/external/Set-Goal-Popup").default
);
// Vue.component('calendar-booked-lessons', require('./components/external/calendar-booked-lessons.vue').default);
//
Vue.component(
    "chart-report",
    require("./components/external/chart-report").default
);
//
// // backend components
Vue.component(
    "benefits-list",
    require("./components/backend/Benefits.vue").default
);
Vue.component(
    "form-benefits",
    require("./components/backend/FormBenefits.vue").default
);
Vue.component(
    "booking-steps-list",
    require("./components/backend/BookingSteps.vue").default
);
Vue.component(
    "how-this-works-list",
    require("./components/backend/HowThisWorks.vue").default
);
Vue.component(
    "backend-lessons-list",
    require("./components/backend/LessonsList.vue").default
);
Vue.component(
    "backend-students-list",
    require("./components/backend/StudentsList.vue").default
);
Vue.component(
    "backend-instructors-list",
    require("./components/backend/InstructorsList.vue").default
);
Vue.component(
    "backend-students-dashboard-list",
    require("./components/backend/UsersDashboardList.vue").default
);
Vue.component(
    "backend-instructors-dashboard-list",
    require("./components/backend/InstructorsDashboardList.vue").default
);
Vue.component(
    "backend-lessons-dashboard-list",
    require("./components/backend/LessonsDashboardList.vue").default
);
Vue.component(
    "modal-invate",
    require("./components/backend/modal-invate.vue").default
);
Vue.component(
    "map-report",
    require("./components/backend/map-report.vue").default
);
Vue.component(
    "magnific-popup-modal-success",
    require("./components/external/MagnificPopupModal").default
);
Vue.component(
    "admin-profile-data-update-form",
    require("./components/backend/AdminProfileDataUpdateForm.vue").default
);

Vue.component(
    "backend-payments-list",
    require("./components/backend/PaymentsList").default
);

Vue.component(
    "backend-demographic-report",
    require("./components/backend/DemographicReport").default
);
Vue.component(
    "backend-geographic-report",
    require("./components/backend/GeographicReport").default
);
Vue.component(
    "backend-other-report",
    require("./components/backend/OtherReport").default
);
Vue.component(
    "backend-overview-report",
    require("./components/backend/OverviewReport").default
);
Vue.component(
    "backend-genres-list",
    require("./components/backend/GenresList").default
);
Vue.component(
    "backend-genre-categories-list",
    require("./components/backend/GenreCategoriesList").default
);
Vue.component(
    "backend-faqs-list",
    require("./components/backend/FaqsList").default
);
Vue.component(
    "backend-faq-categories-list",
    require("./components/backend/FaqCategoriesList").default
);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

const slickSingle = (window.slickSingle = {
    arrows: false,
    dots: true
});

var app = new Vue({
    el: "#app",
    store,
    directives: { Sticky },
    data() {
        return {
            modalSuccess: null
        };
    },
    mounted() {
        this.modalSuccess = this.$refs.modalSuccess;
    }
});

jQuery(document).ready(function() {
    if (Cookies.get("instructorRegistered") == 1) {
        app.$refs.modalSuccess.open();
        Cookies.remove("instructorRegistered", {
            path: "/"
        });
    }
});

setTimeout(function() {
    if (location.hash) {
        window.scrollTo(0, 0);
        var target = location.hash;
        if ($(target).length) {
            smoothScrollTo($(target));
        }
    }
}, 1);

$(document).on("click", ".scroll-to", function() {
    var target = $(this.hash);
    smoothScrollTo(target);
});

function smoothScrollTo(target) {
    var top = target.offset().top;
    if ($(window).width() > 992) {
        top = top - 80;
    }
    if (target.length) {
        $("html,body").animate(
            {
                scrollTop: top
            },
            1000
        );
    }
}

// Cache selectors
var lastId,
    topMenu = $("#top-menu"),
    topMenuHeight = topMenu.outerHeight() + 15,
    // All list items
    menuItems = topMenu.find("a"),
    // Anchors corresponding to menu items
    scrollItems = menuItems.map(function() {
        var item = $($(this).attr("href"));
        if (item.length) {
            return item;
        }
    });

// Bind click handler to menu items
// so we can get a fancy scroll animation
menuItems.click(function(e) {
    var href = $(this).attr("href"),
        offsetTop = href === "#" ? 0 : $(href).offset().top - topMenuHeight + 1;
    $("html, body")
        .stop()
        .animate(
            {
                scrollTop: offsetTop
            },
            300
        );
    e.preventDefault();
});

// Bind to scroll
$(window).scroll(function() {
    // Get container scroll position
    var fromTop = $(this).scrollTop() + topMenuHeight;

    // Get id of current scroll item
    var cur = scrollItems.map(function() {
        if ($(this).offset().top < fromTop + 300) return this;
    });
    // Get the id of the current element
    cur = cur[cur.length - 1];
    var id = cur && cur.length ? cur[0].id : "";

    if (lastId !== id) {
        lastId = id;
        // Set/remove active class
        menuItems
            .parent()
            .removeClass("active")
            .end()
            .filter("[href='#" + id + "']")
            .parent()
            .addClass("active");
    }
});

$(".open-login-popup-link").on("click", function() {
    app.$root.$emit("showLoginFormModal");
});

$(".gallery-wrapper").magnificPopup({
    delegate: ".g-item",
    callbacks: {
        elementParse: function(item) {
            // the class name
            if (item.el[0].className == "video-link g-item") {
                item.type = "iframe";
            } else {
                item.type = "image";
            }
        }
    },
    gallery: { enabled: true },
    type: "image"
});

$(".user-menu-item").on("click", function(e) {
    e.preventDefault();
    $(this)
        .closest(".user-menu")
        .find(".drop-menu")
        .toggleClass("open");
});

$(document).mouseup(function(e) {
    var div = $(".user-menu");
    if (!div.is(e.target) && div.has(e.target).length === 0) {
        div.find(".drop-menu").removeClass("open");
    }
});

if (document.getElementById("copyButton")) {
    document.getElementById("copyButton").addEventListener("click", function() {
        copyToClipboard(document.getElementById("copyTarget"));
    });
}

function copyToClipboard(elem) {
    // create hidden text element, if it doesn't already exist
    var targetId = "_hiddenCopyText_";
    var isInput = elem.tagName === "INPUT" || elem.tagName === "TEXTAREA";
    var origSelectionStart, origSelectionEnd;
    if (isInput) {
        // can just use the original source element for the selection and copy
        target = elem;
        origSelectionStart = elem.selectionStart;
        origSelectionEnd = elem.selectionEnd;
    } else {
        // must use a temporary form element for the selection and copy
        target = document.getElementById(targetId);
        if (!target) {
            var target = document.createElement("textarea");
            target.style.position = "absolute";
            target.style.left = "-9999px";
            target.style.top = "0";
            target.id = targetId;
            document.body.appendChild(target);
        }
        target.textContent = elem.textContent;
    }
    // select the content
    var currentFocus = document.activeElement;
    target.focus();
    target.setSelectionRange(0, target.value.length);

    // copy the selection
    var succeed;
    try {
        succeed = document.execCommand("copy");
    } catch (e) {
        succeed = false;
    }
    // restore original focus
    if (currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
    }

    if (isInput) {
        // restore prior selection
        elem.setSelectionRange(origSelectionStart, origSelectionEnd);
    } else {
        // clear temporary content
        target.textContent = "";
    }
    return succeed;
}

require("jquery-match-height");

$(".result-wrapper-items .item, .upcoming-lessons .item").matchHeight({
    byRow: true
});

// axios.get('/api/lessons/upcoming-nearby/336?distance=2000')
//     .then(response => {

//     })
//     .catch(error => this.apiHandleError(error))

$(document).on("click", ".tab-trigger", function(e) {
    var dataTab = $(this)
        .find("a")
        .data("tab");
    $(".tab-trigger").removeClass("active");
    $(this).addClass("active");
    $(".profile-tab-content").removeClass("active");
    $('.profile-tab-content[data-tab="' + dataTab + '"]').addClass("active");
});

const instructorTabBtn = document.querySelector(".instructor--tab-tg");
const lessonsTabBtn = document.querySelector(".lesson--tab-tg");
const instructorTabWrap = document.querySelector(".tg--instructor-tab");
const lessonsTabWrap = document.querySelector(".tg--lesson-tab");
const popularInstructorsTab = document.querySelector(
    ".popular-section--instructors"
);
const popularLessonsTab = document.querySelector(".popular-section--genres");

if (instructorTabBtn) {
    instructorTabBtn.addEventListener("click", () => {
        lessonsTabBtn.classList.remove("active-tg-tab");
        instructorTabBtn.classList.add("active-tg-tab");
        instructorTabWrap.classList.remove("tg--tab-hidden");
        lessonsTabWrap.classList.add("tg--tab-hidden");
        popularInstructorsTab.classList.remove("popular-section--hidden");
        popularLessonsTab.classList.add("popular-section--hidden");
    });
}

if (lessonsTabBtn) {
    lessonsTabBtn.addEventListener("click", () => {
        lessonsTabBtn.classList.add("active-tg-tab");
        instructorTabBtn.classList.remove("active-tg-tab");
        instructorTabWrap.classList.add("tg--tab-hidden");
        lessonsTabWrap.classList.remove("tg--tab-hidden");
        popularInstructorsTab.classList.add("popular-section--hidden");
        popularLessonsTab.classList.remove("popular-section--hidden");
    });
}
