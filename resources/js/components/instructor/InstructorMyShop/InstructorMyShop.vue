<template>
  <div class="instructor-my-shop">
    <div class="instructor-my-shop__container">
      <filter-header
        heading="My Shop"
        :filters="filters"
        @filter-changed="filterChanged($event)"
        :button="button"
        @button-clicked="addLesson()"
      />

      <div class="instructor-my-shop__content">
        <anim-loader v-if="isLoading" />
        <video-lessons-list
          v-else
          :lessons="lessons"
          :options-menu-items="optionsMenuItems"
          @options-menu-item-clicked="optionsMenuItemClicked($event)"
          card-button="more-info"
          popup-button="watch-instructor"
          :is-instructor-view="true"
        />
      </div>

      <div class="instructor-my-shop__footer">
        <pagination
          v-if="!isLoading"
          @page-changed="changePage($event)"
          :current-page="pagination.currentPage"
          :page-count="pagination.pageCount"
        />
      </div>
    </div>
    <add-lesson-popup :user-genres="userGenres" ref="addLessonPopup" />
    <confirmation-popup ref="confirmationPopup" />
  </div>
</template>

<script>
import FilterHeader from "../FilterHeader/FilterHeader.vue";
import VideoLessonsList from "../../student/VideoLessonsList/VideoLessonsList.vue";
import Pagination from "../../student/Pagination/Pagination.vue";
import AddLessonPopup from "../AddLessonPopup/AddLessonPopup.vue";
import ConfirmationPopup from "../ConfirmationPopup/ConfirmationPopup.vue";
import AnimLoader from "../../cart/AnimLoader/AnimLoader.vue";
import lessonService from "../../../services/lessonService";
import instructorService from "../../../services/instructorService";
import urlHelper from "../../../helpers/urlHelper";

export default {
  name: "InstructorMyShop",
  props: ["userGenres"],
  components: {
    FilterHeader,
    VideoLessonsList,
    Pagination,
    AddLessonPopup,
    ConfirmationPopup,
    AnimLoader,
  },
  data() {
    return {
      isLoading: false,
      pagination: {
        currentPage: 1,
        pageCount: 1,
      },
      button: {
        text: "Add Lesson",
        image: "/images/plus-icon-white.svg",
      },
      filters: [
        {
          type: "select",
          title: "Sort by",
          options: [
            { 
              label: "Creation date newest to oldest",
              code: "created_at_desc",
            },
            { 
              label: "Creation date oldest to newest",
              code: "created_at_asc",
            },
            { 
              label: "Price low to high",
              code: "price_asc",
            },
            { 
              label: "Price high to low",
              code: "price_desc",
            },
          ],
          placeholder: "Sort by",
        },
        {
          type: "multiselect",
          title: "Genres",
          value: [],
          options: [],
          placeholder: "Select genres",
        },
        {
          type: "select",
          title: "Content",
          options: [
            { 
              label: "With video and documents",
              code: "with_video_and_documents",
            },
            { 
              label: "Only with video",
              code: "with_video",
            },
            { 
              label: "Only with documents",
              code: "with_documents",
            },
          ],
          placeholder: "Content",
        },
        // {
        //   type: "search",
        //   title: "Search by Name",
        //   placeholder: "Search by Name",
        //   length: "semiwide",
        // },
      ],
      selectedGenres: [],
      selectedSortBy: "created_at_desc",
      selectedContent: "with_video_and_documents",
      optionsMenuItems: [
        { text: "Edit lesson" },
        { text: "Delete lesson", red: true },
      ],
      lessons: [],
    };
  },
  created() {
    const params = urlHelper.parseQueryParams();

    this.loadLessons();
  },
  methods: {
    async loadLessons() {
      this.isLoading = true;
      const genres = await lessonService.myShopGenres();
      this.filters[1].options = genres.map((item, index) => {
        return {
          code: item.genre_id,
          label: item.title,
        }
      });
      const data = await lessonService.myShopLessons({
        page: this.pagination.currentPage,
        sortBy: this.selectedSortBy,
        genres: this.selectedGenres,
        content: this.selectedContent,
      });
      this.lessons = data.lessons;
      this.pagination.currentPage = data.pagination.currentPage;
      this.pagination.pageCount = data.pagination.pageCount;
      this.isLoading = false;
      urlHelper.updateQueryParams({ 
        page: this.pagination.currentPage == 1 ? null : this.pagination.currentPage,
      }, true);
    },
    changePage(pageNum) {
      this.pagination.currentPage = pageNum;
      this.loadLessons();
    },
    filterChanged({ value, filterIndex }) {
      switch (filterIndex) {
        case 0:
          this.selectedSortBy = value.code;
          break;
        case 1:
          this.selectedGenres = value.map((item) => {
            return item.code;
          });
          break;
        case 2:
          this.selectedContent = value.code;
          break;
      }
      this.loadLessons();
    },
    addLesson() {
      this.$refs.addLessonPopup.showPopup();
    },
    editLesson(lesson) {
      let parts = lesson.preview.split("/");
      const preview = parts[parts.length - 1];
      parts = lesson.video.split("/");
      const video = parts[parts.length - 1];

      const documents = lesson.documents.map((item) => {
        return lesson.documentsPath + item.name;
      });
      while (documents.length < 5) {
        documents.push(null);
      }

      this.$refs.addLessonPopup.showPopup(
        true,
        {
          genre: lesson.genre.id,
          title: lesson.title,
          price: lesson.price,
          description: lesson.description,
          video: video,
          preview: preview,
        },
        lesson.id,
        lesson.preview,
        documents,
      );
    },
    confirmDelete(text, action) {
      this.$refs.confirmationPopup.showConfirm(text, () => {
        action();
      });
    },
    async deleteLesson(lessonId) {
      this.confirmDelete(
        "Are you sure you want to delete this lesson?",
        async () => {
          await instructorService.deleteLesson(lessonId);
          location.reload();
        }
      );
    },
    optionsMenuItemClicked({ menuItemIndex, lesson }) {
      if (menuItemIndex == 0) {
        this.editLesson(lesson);
      } else if (menuItemIndex == 1) {
        this.deleteLesson(lesson.id);
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./InstructorMyShop.scss";
</style>