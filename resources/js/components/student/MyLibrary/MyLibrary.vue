<template>
    <div class="my-library">
        <div class="my-library__container">
            <filter-header
                heading="My Library"
                :filters="filters"
                @filter-changed="filterChanged($event)"
                :button="button"
            />

            <div class="my-library__content">
                <anim-loader v-if="isLoading" />
                <video-lessons-list
                    v-else
                    :lessons="lessons"
                    :can-book="canBook"
                    show-instructor-info
                    purchased
                    card-button="watch-student"
                    popup-button="watch-student"
                />
            </div>

            <div class="my-library__footer">
                <pagination
                    @page-changed="changePage($event)"
                    :current-page="currentPage"
                    :page-count="pageCount"
                />
            </div>
        </div>
    </div>
</template>

<script>
import FilterHeader from "../../instructor/FilterHeader/FilterHeader.vue";
import VideoLessonsList from "../VideoLessonsList/VideoLessonsList.vue";
import Pagination from "../Pagination/Pagination.vue";
import AnimLoader from "../../cart/AnimLoader/AnimLoader.vue";
import lessonService from "../../../services/lessonService";

export default {
    name: "MyLibrary",
    components: {
        FilterHeader,
        VideoLessonsList,
        Pagination,
        AnimLoader
    },
    props: {
        canBook: {
            type: Boolean,
            default: false
        }
    },
    async mounted() {
        this.isLoading = true;
        this.lessons = await lessonService.myLibraryLessons();
        this.isLoading = false;
    },
    methods: {
        changePage(pageNum) {
            console.log("changePage", pageNum);
        }
    },
    data() {
        return {
            isLoading: false,
            currentPage: 1,
            pageCount: 1,
            button: {
                type: "link",
                text: "Shop for Lessons and Tutorials",
                href: "/globalshop"
            },
            filters: [
                {
                    type: "select",
                    title: "Genre",
                    options: [
                        "Cheerleading",
                        "Cheerleading 2",
                        "Cheerleading 3"
                    ],
                    placeholder: "Genre"
                },
                {
                    type: "search",
                    title: "",
                    placeholder: "Enter the question",
                    length: "wide"
                }
            ],
            selectedGenre: "Cheerleading",
            lessons: []
        };
    }
};
</script>

<style lang="scss" scoped>
@import "./MyLibrary.scss";
</style>
