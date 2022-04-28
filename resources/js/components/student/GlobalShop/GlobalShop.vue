<template>
  <div class="global-shop">
    <div class="global-shop__container">

      <div class="global-shop__content">
        <div class="global-shop__content-header">
          <h1 class="global-shop__heading"> Shop for Lessons and Tutorials</h1>
          <pagination
            @page-changed="changePage($event)"
            :current-page="pagination.currentPage"
            :page-count="pagination.pageCount"
          />
        </div>
        <div class="global-shop__list">
          <anim-loader v-if="isLoading" />
          <video-lessons-list
            v-else
            :can-book="canBook"
            :lessons="lessons"
            show-instructor-info
            card-button="more-info"
            popup-button="add-to-cart"
          />
        </div>
      </div>

      <div class="global-shop__footer">
        <pagination
          v-if="!isLoading"
          @page-changed="changePage($event)"
          :current-page="pagination.currentPage"
          :page-count="pagination.pageCount"
        />
      </div>
    </div>
  </div>
</template>

<script>
import Pagination from "../Pagination/Pagination.vue";
import VideoLessonsList from "../VideoLessonsList/VideoLessonsList.vue";
import AnimLoader from "../../cart/AnimLoader/AnimLoader.vue";
import lessonService from "../../../services/lessonService";
import urlHelper from "../../../helpers/urlHelper";

export default {
  name: "GlobalShop",
  components: {
    Pagination,
    VideoLessonsList,
    AnimLoader,
  },
  props: {
    canBook: {
        type: Boolean,
        default: false,
    },
    preloadedLessons: {
      type: Array,
      default: () => {
        return [];
      },
    },
    preloadedPagination: {
      type: Object,
      default: () => {
        return {}
      }
    }
  },
  methods: {
    async loadLessons(params = {}) {
      this.isLoading = true;
      const data = await lessonService.preRecordedLessons({ 
        page: this.pagination.currentPage, 
        ...params,
      });

      this.pagination.currentPage = data.pagination.currentPage;
      this.pagination.pageCount = data.pagination.pageCount;
      this.lessons = data.lessons;
      this.isLoading = false;
      urlHelper.updateQueryParams({ 
        page: data.currentPage == 1 ? null : data.currentPage,
        ...params,
      }, false);
    },
    changePage(pageNum) {
      this.pagination.currentPage = pageNum;
      this.loadLessons();
    },
  },
  created() {
    const params = urlHelper.parseQueryParams();
    urlHelper.updateQueryParams({ 
      page: params.page == 1 ? null : params.page,
    }, false);

    this.$root.$on("globalShopLoadLessons", (params) => {
      this.loadLessons(params);
    });
  },
  data() {
    return {
      isLoading: false,
      pagination: {
        currentPage: this.preloadedPagination.pagination.current_page,
        pageCount: this.preloadedPagination.pagination.total_pages
      },
      lessons: this.preloadedLessons,
    };
  },
};
</script>

<style lang="scss" scoped>
@import "./GlobalShop.scss";
</style>