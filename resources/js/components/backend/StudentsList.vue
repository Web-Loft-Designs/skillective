<template>
  <div>
    <div class="users-table-header">
      <div class="table-top d-flex align-items-center">
        <h2>Clients</h2>
        <div class="ml-3 d-flex align-items-center">
          <button
            @click.prevent="toggleShowOnly('active')"
            :class="{ active: showOnly == 'active' }"
          >
            Active
          </button>
          <button
            @click.prevent="toggleShowOnly('approved')"
            :class="{ active: showOnly == 'approved' }"
          >
            Incomplete
          </button>
          <button
            @click.prevent="toggleShowOnly('suspended')"
            :class="{ active: showOnly == 'suspended' }"
          >
            Suspended
          </button>
        </div>
      </div>
      <div
        class="d-flex mt-5 responsive-mobile justify-content-between align-items-center"
      >
        <div class="d-flex responsive-mobile align-items-center">
          <div
            v-if="selectedItems.length > 0"
            class="d-flex align-items-center"
          >
            <span @click="blockManyUsers" class="btn btn-danger mr-2"
              >Delete {{ selectedItems.length }} users</span
            >
            <span @click="notifyManyUsers" class="btn btn-notify mr-2"
              >Contact {{ selectedItems.length }} users</span
            >
          </div>
          <input type="text" v-model="searchString" placeholder="Search user" />
        </div>
        <div>
          <!--<modal-invate :text-title="'Invite Users'" :text-button="'Invite User'"></modal-invate>-->
        </div>
      </div>

      <form
        method="post"
        action="/backend/login-as"
        ref="loginAsForm"
        style="display: none"
      >
        <input type="hidden" name="_token" :value="csrf" />
        <input type="hidden" name="user_id" :value="loginAsUserId" />
      </form>
    </div>
    <div class="table-responsives">
      <div v-if="errorText" class="has-error">{{ errorText }}</div>
      <div v-if="successText" class="has-success">{{ successText }}</div>

      <table class="table">
        <thead>
          <tr>
            <th scope="col">
              <span class="checkbox-wrapper">
                <label for="checkAll">
                  <input
                    @change="selectAll"
                    :indeterminate.prop="indeterminate"
                    v-model="allSelected"
                    id="checkAll"
                    type="checkbox"
                  />
                  <span
                    class="checkmark"
                    :class="{ indeterminate: indeterminate === true }"
                  ></span>
                </label>
              </span>
            </th>
            <th class="w-55 hidden-in-mobile"></th>
            <th class="w-100px">Instagram</th>
            <th class="w-140">Name</th>
            <th class="w-140 hidden-in-mobile">Email</th>
            <th class="phone-width hidden-in-mobile">Phone</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(user, index) in listItems">
            <td>
              <span class="checkbox-wrapper"
                ><label
                  ><input
                    @change="select"
                    type="checkbox"
                    v-model="selectedItems"
                    :value="user.id" /><span class="checkmark"></span></label
              ></span>
            </td>
            <td class="hidden-in-mobile">
              <img :src="user.profile.image" alt="" />
            </td>
            <td class="width-fix">
              <div class="width-fix-content">
                <a
                  v-if="user.profile.instagram_handle != null"
                  :href="
                    'https://www.instagram.com/' + user.profile.instagram_handle
                  "
                  target="_blank"
                  >{{ "@" + user.profile.instagram_handle }}</a
                >
              </div>
            </td>
            <td class="width-fix">
              <div class="width-fix-content">
                <a :href="'/profile/' + user.id" class="link-to-profile">{{
                  user.full_name
                }}</a>
              </div>
            </td>
            <td class="width-fix hidden-in-mobile">
              <div class="width-fix-content">{{ user.email }}</div>
            </td>
            <td class="hidden-in-mobile">{{ user.profile.mobile_phone }}</td>
            <td class="action">
              <div class="dropdown">
                <button
                  class="btn btn-left"
                  @click.prevent="showUserPublicProfile(user.id)"
                >
                  View
                </button>
                <button
                  class="btn btn-outline-secondary dropdown-toggle"
                  type="button"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                ></button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <a class="dropdown-item" :href="'/profile/edit/' + user.id"
                    >Edit profile</a
                  >
                  <a
                    class="dropdown-item"
                    href="#"
                    @click.prevent="notifyInstructor(user.id)"
                    >Contact</a
                  >
                  <a
                    class="dropdown-item"
                    :href="'/backend/instructors?invited_by=' + user.id"
                    >Invited instructors</a
                  >
                  <a
                    v-if="user.status != 'blocked'"
                    class="dropdown-item"
                    href="#"
                    @click.prevent="loginAsUser(user)"
                    >Login</a
                  >
                  <a
                    v-if="user.status != 'blocked'"
                    class="dropdown-item red"
                    href="#"
                    @click.prevent="blockUser(user)"
                    >Suspend</a
                  >
                  <a
                    class="dropdown-item red"
                    href="#"
                    @click.prevent="deleteUser(user)"
                    >Delete</a
                  >
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="row justify-content-center" v-if="pagination.total_pages > 1">
      <div class="col-6 select-show">
        <label>Show</label>
        <select v-model="pagination.per_page" @change="onChangePerPage">
          <option value="25">25</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>
      <div
        class="col-6 d-flex justify-content-end align-items-center custom-pag"
      >
        <span
          >{{ firstListItemNumber }}-{{ lastListItemNumber }} of
          {{ pagination.total }}</span
        >
        <paginate
          :page-count="pagination.total_pages"
          :force-page="pagination.current_page"
          :prev-text="'Prev'"
          :next-text="'Next'"
          :click-handler="paginatorClickCallback"
          :container-class="'clients-pagination'"
        >
        </paginate>
      </div>
    </div>
  </div>
</template>

<script>
import siteAPI from "../../mixins/siteAPI.js";
import skillectiveHelper from "../../mixins/skillectiveHelper.js";
import Paginate from "vuejs-paginate";

export default {
  mixins: [siteAPI, skillectiveHelper],
  props: {
    students: null,
    studentsMeta: {},
  },
  components: {
    Paginate,
  },
  data() {
    return {
      listItems: [],
      selectedItems: [],
      searchString: "",
      showOnly: "active",
      allSelected: false,
      indeterminate: false,
      pagination: {
        total: 0,
        total_pages: 0,
        current_page: 0,
        per_page: 0,
      },
      loginAsUserId: null,
      csrf: null,
    };
  },
  methods: {
    selectAll: function () {
      this.selectedItems = [];

      if (this.allSelected) {
        this.indeterminate = false;
        for (let user in this.listItems) {
          this.selectedItems.push(this.listItems[user].id.toString());
        }
      }
    },
    select: function () {
      if (this.listItems.length == this.selectedItems.length) {
        this.allSelected = 1;
        this.indeterminate = false;
      } else if (this.selectedItems.length === 0) {
        this.allSelected = 0;
        this.indeterminate = false;
      } else {
        this.allSelected = 0;
        this.indeterminate = true;
      }
    },
    toggleShowOnly(status) {
      this.showOnly = status;
      this.allSelected = 0;
      this.selectedItems = [];
      this.pagination.current_page = 1;
      this.getUsers();
    },
    paginatorClickCallback(pageNum) {
      this.pagination.current_page = pageNum;
      this.getUsers();
    },
    onChangePerPage() {
      this.pagination.current_page = 1;
      Cookies.set("adminStudentsPerPage", this.pagination.per_page);
      this.getUsers();
    },
    blockManyUsers() {
      this.apiPost("/api/admin/users/suspend", { users: this.selectedItems });
    },
    loginAsUser(user) {
      this.loginAsUserId = user.id;
      this.csrf = document.head.querySelector(
        'meta[name="csrf-token"]'
      ).content;
      setTimeout(() => {
        this.$refs.loginAsForm.submit();
      }, 50);
    },
    notifyInstructor(client) {
      var temp = [];
      temp.push({ id: client });
      this.$root.$emit("initNotificationsForm", temp);
    },
    notifyManyUsers() {
      this.$root.$emit("initNotificationsForm", this.selectedItems);
    },
    blockUser(user) {
      this.apiDelete("/api/admin/users/" + user.id);
    },
    notifyUser(user) {
      console.log(3);
      this.$root.$emit("initNotificationsForm", [user]);
    },
    searchUsers() {
      this.pagination.current_page = 1;
      this.getUsers();
    },
    deleteUser(user) {
      this.apiDelete("/api/admin/users/delete/" + user.id);
    },
    getUsers() {
      let queryParams = {};

      queryParams.status = this.showOnly;

      if (this.pagination.current_page != undefined)
        queryParams.page = this.pagination.current_page;
      else queryParams.page = 1;

      if (this.searchString != "") queryParams.s = this.searchString;

      this.updateUrlQueryParams(queryParams);

      this.apiGet("/api/admin/students", {
        params: queryParams,
      });
    },
    componentHandleGetResponse(responseData) {
      this.listItems = responseData.data.data;
      this.selectedItems = [];
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
      this.allSelected = false;
      this.indeterminate = false;
    },
    componentHandleDeleteResponse(responseData) {
      this.getUsers();
    },
    componentHandlePostResponse(responseData) {
      this.getUsers();
    },
    showUserPublicProfile(userId) {
      window.location = "/profile/" + userId;
    },
  },
  created: function () {
    this.listItems = this.students;
    if (
      this.studentsMeta != undefined &&
      this.studentsMeta.pagination != undefined
    ) {
      this.pagination.count = this.studentsMeta.pagination.count;
      this.pagination.total = this.studentsMeta.pagination.total;
      this.pagination.total_pages = this.studentsMeta.pagination.total_pages;
      this.pagination.current_page = this.studentsMeta.pagination.current_page;
      this.pagination.per_page = this.studentsMeta.pagination.per_page;
    }
    if (this.getUrlParameter("s"))
      this.searchString = this.getUrlParameter("s");
    if (this.getUrlParameter("status"))
      this.showOnly = this.getUrlParameter("status");

    this.debouncedGetUsers = _.debounce(this.searchUsers, 500);
  },
  watch: {
    searchString: function (newSearchString, oldSearchString) {
      this.debouncedGetUsers();
    },
  },
  computed: {
    firstListItemNumber: function () {
      return (
        this.pagination.current_page * this.pagination.per_page -
        this.pagination.per_page +
        1
      );
    },
    lastListItemNumber: function () {
      if (this.pagination.count == this.pagination.per_page) {
        return this.firstListItemNumber + this.pagination.per_page - 1;
      } else {
        return this.firstListItemNumber + this.pagination.count - 1;
      }
    },
  },
};
</script>