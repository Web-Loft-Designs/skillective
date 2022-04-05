<template>
    <div :class="{
        'lessons': true,
        'lessons--show-map': showMap,
    }">
        <div class="lessons__container">
            <div class="lessons__col">
                <ul class="lessons__choosed-options">
                    <li v-for="(option, index) in choosedOptions" :key="index">{{ option }}</li>
                </ul>
                <h1 v-if="virtualMode" class="lessons__heading">Virtual lessons</h1>
                <h1 v-else class="lessons__heading">In-Person lessons</h1>
                <additional-lessons-filters />
                <div class="lessons__header">
                    <div class="lessons__header-sort">
                        <span>Sort by</span>
                        <select-with-search 
                            :options="sortOptions" 
                            @value-changed="sortChanged($event)"
                            height="small"
                            ref="sortSelect"
                        />
                    </div>
                    <green-toggle
                        enabledText="Show"
                        disabledText="Hide"
                        v-model="showMap"
                        label="Map"
                    />
                </div>
                <anim-loader v-if="isLoading" />
                <lessons-list
                    v-else
                    :lessons="lessons"
                    :logged-in-as-student="loggedInAsStudent"
                />
                <div class="lessons__footer">
                    <pagination
                        v-if="!isLoading"
                        @page-changed="changePage($event)"
                        :current-page="pagination.currentPage"
                        :page-count="pagination.pageCount"
                    />
                </div>
            </div>
            <div class="lessons__col">

            </div>
        </div>
    </div>
</template>

<script>
import LessonsList from "../LessonsList/LessonsList.vue";
import AdditionalLessonsFilters from "../AdditionalLessonsFilters/AdditionalLessonsFilters.vue";
import SelectWithSearch from "../../student/SelectWithSearch/SelectWithSearch.vue";
import GreenToggle from "../GreenToggle/GreenToggle.vue";
import dateHelper from "../../../helpers/dateHelper";
import urlHelper from "../../../helpers/urlHelper";
import Pagination from "../../student/Pagination/Pagination.vue";
import AnimLoader from "../../cart/AnimLoader/AnimLoader.vue";
import lessonService from "../../../services/lessonService";

export default {
    name: "Lessons",
    components: {
        LessonsList,
        AdditionalLessonsFilters,
        SelectWithSearch,
        GreenToggle,
        Pagination,
        AnimLoader,
    },
    props: {
        preloadedLessons: {
            type: Array,
            default: () => {
                return [];
            },
        },
        genres: {
            type: Array,
            default: () => {
                return [];
            },
        },
        loggedInAsStudent: {
            type: Boolean,
            default: false,
        },
        meta: {
            type: Object,
            default: () => {
                return {};
            },
        },
    },
    created() {
        this.$root.$on("lessonsLoadLessons", (params) => {
            this.loadLessons(params);
        });

        const params = urlHelper.parseQueryParams();
        urlHelper.updateQueryParams({ 
            page: params.page == 1 ? null : params.page,
        }, false);
    },
    mounted() {
        this.parseChoosedOptions();
    },
    data() {
        return {
            showMap: true,
            sortedBy: "start",
            orderBy: "asc",
            choosedOptions: [],
            virtualMode: false,
            sortOptions: [
                {
                    label: "Lowest price",
                    value: "spot_price_asc",
                },
                {
                    label: "Highest price",
                    value: "spot_price_desc",
                },
                {
                    label: "Lesson (Soonest)",
                    value: "start_asc",
                },
                {
                    label: "Lesson (Latest)",
                    value: "start_desc",
                },
            ],
            lessons: this.preloadedLessons,
            isLoading: false,
            pagination: {
                currentPage: this.meta.pagination.current_page,
                pageCount: this.meta.pagination.total_pages,
            },
        }
    },
    watch: {
        showMap(newValue) {
            this.$root.$emit('lessonsToggleMap', newValue);
        }
    },
    methods: {
        parseChoosedOptions() {
            const params = urlHelper.parseQueryParams();
            this.choosedOptions = [];

            let dateFrom = null;
            let dateTo = null;
            let flexibleDays = null;
            let flexibleMonths = null;
            let location = null;

            if (params.lesson_type) {
                this.virtualMode = params.lesson_type == "virtual";
                if (params.lesson_type == "virtual") {
                    this.choosedOptions.push("Learn Virtually");
                } else {
                    this.choosedOptions.push("Learn In-Person");
                }
            }
            if (params.instructor_name) {
                this.choosedOptions.push(params.instructor_name);
            }
            if (params.genre) {
                if (this.genres.length) {
                    this.genres.map((genre) => {
                        if (genre.id == params.genre) {
                            this.choosedOptions.push(genre.title);
                        }
                    });
                }
            }
            if (params.date_from) {
                dateFrom = dateHelper.filterToDate(params.date_from);
            }
            if (params.date_to) {
                dateTo = dateHelper.filterToDate(params.date_to);
            }
            if (params.flexible_days) {
                flexibleDays = params.flexible_days;
            }
            if (params.flexible_months) {
                flexibleMonths = dateHelper.filterToFlexibleMonths(params.flexible_months);
            }
            if (params.location) {
                location = params.location;
            }
            if (params.sortedBy) {
                this.sortedBy = params.sortedBy;
                this.$refs.sortSelect.selectByValue(params.sortedBy);
            }

            if (dateFrom && dateTo) {
                this.choosedOptions.push(dateHelper.formatDateWithoutTime(dateFrom, dateTo));
            }
            if (flexibleDays && flexibleMonths) {
                this.choosedOptions.push(dateHelper.formatFlexibleDate(flexibleDays, flexibleMonths));
            }
            if (location) {
                this.choosedOptions.push(location);
            }
        },
        async loadLessons(params = {}) {
            this.isLoading = true;
            const data = await lessonService.lessons({ 
                page: this.pagination.currentPage, 
                ...params,
            });
            this.pagination.currentPage = data.currentPage;
            this.pagination.pageCount = data.pageCount;
            this.lessons = data.lessons;
            this.isLoading = false;
            urlHelper.updateQueryParams({ 
                page: data.currentPage == 1 ? null : data.currentPage,
                ...params,
            }, false);
            this.parseChoosedOptions();
        },
        changePage(pageNum) {
            this.pagination.currentPage = pageNum;
            this.loadLessons();
        },
        sortChanged(event) {
            if (event.value != this.sortedBy) {
                this.sortedBy = event.value;
                this.loadLessons({
                    sortedBy: this.sortedBy,
                });
            }
        },
    },
};
</script>

<style lang="scss" scoped>
@import "./Lessons.scss";
</style>
