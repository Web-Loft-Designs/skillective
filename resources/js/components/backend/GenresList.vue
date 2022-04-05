<template>
  <div>
    <div class="users-table-header">
      <div class="table-top d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
          <h2>Genres</h2>
          <div class="ml-3 d-flex align-items-center">
            <button
              @click.prevent="toggleShowOnly('active')"
              :class="{ active: showOnly == 'active' }"
            >
              Active
            </button>
            <button
              @click.prevent="toggleShowOnly('disabled')"
              :class="{ active: showOnly == 'disabled' }"
            >
              Disabled
            </button>
          </div>
        </div>

        <div>
          <a
            href="/backend/genres/create"
            class="btn btn-primary pull-right"
            style="margin-top: 3px; margin-bottom: 5px"
            ><img
              width="17"
              src="../../../images/ic_person_add-ic_add.png"
              alt=""
              style="margin-right: 3px"
            />
            Add New</a
          >
        </div>
      </div>
      <div
        class="d-flex mt-3 responsive-mobile justify-content-between align-items-center"
      >
        <div class="d-flex responsive-mobile align-items-center">
          <!--<div v-if="selectedItems.length>0" class="d-flex align-items-center">-->
          <!--<span v-if="showOnly!='disabled'" @click="disabledManyGenres" class="btn btn-danger mr-2">Disabled {{ selectedItems.length }} genres</span>-->
          <!--<span v-if="showOnly!='active'" @click="activeManyGenres" class="btn btn-danger mr-2">Active {{ selectedItems.length }} genres</span>-->
          <!--</div>-->
          <input
            type="text"
            v-model="searchString"
            placeholder="Search genre"
          />
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <div v-if="errorText" class="has-error">{{ errorText }}</div>
      <div v-if="successText" class="has-success">{{ successText }}</div>

      <table class="table">
        <thead>
          <tr>
            <!--<th scope="col">-->
            <!--<span class="checkbox-wrapper">-->
            <!--<label for="checkAll">-->
            <!--<input @change="selectAll" :indeterminate.prop="indeterminate" v-model="allSelected" id="checkAll" type="checkbox"/>-->
            <!--<span class="checkmark" :class="{'indeterminate':indeterminate === true}"></span>-->
            <!--</label>-->
            <!--</span>-->
            <!--</th>-->
            <th class="text-center w-55"></th>
            <th class="w-55"></th>
            <th>Genre name</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(genre, index) in listItems">
            <!--<td><span class="checkbox-wrapper"><label><input @change="select" type="checkbox" v-model="selectedItems" :value="genre.id"/><span class="checkmark"></span></label></span></td>-->
            <td class="text-center"></td>
            <td><img :src="genre.image" alt="" /></td>
            <td>{{ genre.title }}</td>
            <td class="action">
              <div class="dropdown">
                <button
                  class="btn btn-left"
                  @click.prevent="editGenres(genre.id)"
                >
                  Edit
                </button>
                <button
                  class="btn btn-outline-secondary dropdown-toggle"
                  type="button"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                ></button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                  <a
                    v-if="showOnly == 'active'"
                    class="dropdown-item red"
                    href="#"
                    @click.prevent="disableGenre(genre)"
                    >Disable</a
                  >
                  <a
                    v-if="showOnly == 'disabled'"
                    class="dropdown-item"
                    href="#"
                    @click.prevent="activeGenres(genre)"
                    >Active</a
                  >
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="row justify-content-center">
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
        <span v-if="pagination.total_pages > 1"
          >{{ firstListItemNumber }}-{{ lastListItemNumber }} of
          {{ pagination.total }}</span
        >
        <paginate
          v-if="pagination.total_pages > 1"
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
    genres: null,
    genresMeta: {},
  },
  components: {
    Paginate,
  },
  data() {
    return {
      listItems: [],
      selectedItems: [],
      searchString: "",
      category: "",
      showOnly: "active",
      allSelected: false,
      indeterminate: false,
      pagination: {
        total: 0,
        total_pages: 0,
        current_page: 0,
        per_page: 0,
      },
    };
  },
  methods: {
    selectAll: function () {
      this.selectedItems = [];

      if (this.allSelected) {
        this.indeterminate = false;
        for (let genre in this.listItems) {
          this.selectedItems.push(this.listItems[genre].id.toString());
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
      this.category = "";
      this.getGenres();
    },
    paginatorClickCallback(pageNum) {
      this.pagination.current_page = pageNum;
      this.getGenres();
    },
    onChangePerPage() {
      this.pagination.current_page = 1;
      Cookies.set("adminGenresPerPage", this.pagination.per_page);
      this.getGenres();
    },
    //            disabledManyGenres(){
    //                this.apiPost('/api/admin/genres?status=disabled', {genres : this.selectedItems});
    //            },
    //            activeManyGenres(){
    //                this.apiPost('/api/admin/genres?status=active', {genres : this.selectedItems});
    //            },
    activeGenres(genres) {
      this.apiPost("/api/admin/genre/" + genres.id);
    },
    disableGenre(genres) {
      this.apiDelete("/api/admin/genre/" + genres.id);
    },
    searchGenres() {
      this.pagination.current_page = 1;
      this.getGenres();
    },
    getGenres() {
      let queryParams = {};

      queryParams.status = this.showOnly;

      if (this.pagination.current_page != undefined)
        queryParams.page = this.pagination.current_page;
      else queryParams.page = 1;

      if (this.searchString != "") queryParams.s = this.searchString;

      if (this.category != "") queryParams.category = this.category;

      this.updateUrlQueryParams(queryParams);

      this.apiGet("/api/admin/genres", {
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
      this.getGenres();
    },
    componentHandlePostResponse(responseData) {
      this.getGenres();
    },
    editGenres(genresId) {
      window.location = "/backend/genres/" + genresId + "/edit";
    },
  },
  created: function () {
    this.listItems = this.genres;
    if (
      this.genresMeta != undefined &&
      this.genresMeta.pagination != undefined
    ) {
      this.pagination.count = this.genresMeta.pagination.count;
      this.pagination.total = this.genresMeta.pagination.total;
      this.pagination.total_pages = this.genresMeta.pagination.total_pages;
      this.pagination.current_page = this.genresMeta.pagination.current_page;
      this.pagination.per_page = this.genresMeta.pagination.per_page;
    }
    if (this.getUrlParameter("s"))
      this.searchString = this.getUrlParameter("s");
    if (this.getUrlParameter("category"))
      this.category = this.getUrlParameter("category");
    if (this.getUrlParameter("status"))
      this.showOnly = this.getUrlParameter("status");

    this.debouncedGetGenres = _.debounce(this.searchGenres, 500);
  },
  watch: {
    searchString: function (newSearchString, oldSearchString) {
      this.debouncedGetGenres();
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