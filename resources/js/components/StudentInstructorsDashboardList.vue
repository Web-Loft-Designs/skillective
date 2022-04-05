<template>
  <div id="profile-instructors-container">
    <div class="table-top d-flex align-items-center">
      <h2 class="page-title">Instructors</h2>
    </div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th class="w-46" scope="col">-</th>
            <th class="w-46" scope="col">-</th>
            <th class="w-46" scope="col">#</th>
            <th class="w-55" scope="col"></th>
            <th class="w-100px" scope="col">Instagram</th>
            <th class="w-140" scope="col">Name</th>
            <th scope="col">Genre</th>
            <th scope="col"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(instructor, index) in listItems" v-bind:key="index">
            <td class="w-l bell-wrapper">
              <div class="bell-wrapper--inner">
                <img
                  class="lesson-type-img"
                  src="/images/in-person-green.svg"
                  alt=""
                />
                <student-instructor-geo-notifications-toggle
                  v-bind:instructor-id="instructor.id"
                  v-bind:geo-notifications-allowed="
                    instructor.geoNotificationsAllowed ? true : false
                  "
                ></student-instructor-geo-notifications-toggle>
              </div>
              <div class="bell-wrapper--inner">
                <img
                  class="lesson-type-img"
                  src="/images/private-lesson-green.svg"
                  alt=""
                />
                <student-instructor-virtual-lesson-notifications-toggle
                  v-bind:instructor-id="instructor.id"
                  v-bind:virtual-lesson-notifications-allowed="
                    instructor.virtualNotificationsAllowed ? true : false
                  "
                ></student-instructor-virtual-lesson-notifications-toggle>
              </div>
            </td>
            <td class="w-l">
              <favorite-instructor
                v-bind:instructor-id="instructor.id"
                v-bind:is-favorite="instructor.isFavorite ? true : false"
              ></favorite-instructor>
            </td>
            <td class="w-l">
              {{ pagination.total - firstListItemNumber - index + 1 }}
            </td>
            <td class="w-a"><img :src="instructor.profile.image" /></td>
            <td class="width-fix">
              <div class="width-fix-content">
                <a
                  v-if="instructor.profile.instagram_handle != null"
                  :href="
                    'https://www.instagram.com/' +
                    instructor.profile.instagram_handle
                  "
                  target="_blank"
                  >{{ instructor.profile.instagram_handle }}</a
                >
              </div>
            </td>
            <td class="width-fix">
              <div class="width-fix-content">
                <a
                  :href="'/profile/' + instructor.id"
                  class="link-to-profile"
                  >{{ instructor.full_name }}</a
                >
              </div>
            </td>
            <td
              class="cusotm-w"
              v-html="getUserGenresList(instructor.genres)"
            ></td>
            <!--<td>-->
            <!--<span v-if="instructor.profile.notification_methods.indexOf('email')!==-1" >email</span>-->
            <!--<span v-if="instructor.profile.notification_methods.indexOf('sms')!==-1" >sms</span>-->
            <!--<span v-if="instructor.profile.notification_methods.indexOf('whatsapp')!==-1" >whatsapp</span>-->
            <!--</td>-->
            <td>
              <!--<span class="btn btn-default" @click="notifyInstructor" v-if="instructor.profile.notification_methods.length>0">Notify</span>-->
              <span class="btn btn-danger" @click="deleteInstructor(instructor)"
                >Delete</span
              >
            </td>
          </tr>
        </tbody>
      </table>
      <div v-if="listItems.length <= 0">
        <p class="text-center">Add favorite instructors</p>
      </div>
    </div>

    <a
      v-if="this.pagination.total > 5"
      href="/student/instructors"
      class="btn btn-secondary btn-block"
      >View all</a
    >

    <confirmation-popup ref="confirmationPopup" />
  </div>
</template>

<script>
import siteAPI from "../mixins/siteAPI.js";
import skillectiveHelper from "../mixins/skillectiveHelper.js";
import ConfirmationPopup from "./instructor/ConfirmationPopup/ConfirmationPopup.vue";

export default {
  mixins: [siteAPI, skillectiveHelper],
  components : {
    ConfirmationPopup,
  },
  props: {
    instructors: null,
    instructorsMeta: {},
  },
  data() {
    return {
      pagination: {
        total: 0,
        total_pages: 0,
        current_page: 0,
        per_page: 0,
      },
    };
  },
  methods: {
    confirmDelete(text, action) {
      this.$refs.confirmationPopup.showConfirm(text, () => {
        action();
      });
    },
    getInstructors() {
      let queryParams = {};
      queryParams.limit = 5;

      this.apiGet("/api/student/instructors", {
        params: queryParams,
      });
    },
    deleteInstructor(instructor) {
      this.confirmDelete("Are you sure you want to delete this instructor?", () => {
        this.apiDelete("/api/student/instructor/" + instructor.id);
      });
    },
    componentHandleDeleteResponse(responseData) {
      this.getInstructors();
    },
    componentHandleGetResponse(responseData) {
      this.listItems = responseData.data.data;
      if (
        responseData.data.meta != undefined &&
        responseData.data.meta.pagination != undefined
      ) {
        this.pagination.count = responseData.data.meta.pagination.count;
        this.pagination.total = responseData.data.meta.pagination.total;
        this.pagination.total_pages =
          responseData.data.meta.pagination.total_pages;
        this.pagination.current_page =
          responseData.data.meta.pagination.current_page;
        this.pagination.per_page = responseData.data.meta.pagination.per_page;
      }
    },
    getUserGenresList(userGenres) {
      var _titles = [];
      if (userGenres.length > 3) {
        for (var i = 0; i < 3; i++) _titles.push(userGenres[i].title);
        if (_titles.length > 0)
          return (
            '<span class="bars">' +
            _titles.join('</span><span class="bars">') +
            "</span>"
          );
        return "";
      } else {
        for (var i = 0; i < userGenres.length; i++)
          _titles.push(userGenres[i].title);
        if (_titles.length > 0)
          return (
            '<span class="bars">' +
            _titles.join('</span><span class="bars">') +
            "</span>"
          );
        return "";
      }
    },
  },
  created: function () {
    this.listItems = this.instructors;
    if (
      this.instructorsMeta != undefined &&
      this.instructorsMeta.pagination != undefined
    ) {
      this.pagination.count = this.instructorsMeta.pagination.count;
      this.pagination.total = this.instructorsMeta.pagination.total;
      this.pagination.total_pages = this.instructorsMeta.pagination.total_pages;
      this.pagination.current_page = this.instructorsMeta.pagination.current_page;
      this.pagination.per_page = this.instructorsMeta.pagination.per_page;
    }
    //            this.$set(this.listItems, 'notify', [
    //                {
    //                    property: false
    //                }
    //            ]);
  },
  computed: {
    firstListItemNumber: function () {
      return (
        this.pagination.current_page * this.pagination.per_page -
        this.pagination.per_page +
        1
      );
    },
  },
};
</script>


