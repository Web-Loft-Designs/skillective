<template>
    <div
        v-if="isLoading || lessons.length > 0"
        class="upcoming-lessons-near-you"
    >
        <h2 class="upcoming-lessons-near-you__heading">
            Upcoming Lessons Near You
        </h2>

        <anim-loader v-if="isLoading" />

        <slick v-else :options="slickOptions" ref="upcomingLessonsNearYouSlick">
            <div
                class="nearby-lesson__outer"
                v-for="(lesson, index) in lessons"
                :key="index"
            >
                <div class="nearby-lesson">
                    <div class="nearby-lesson__body">
                        <a
                            class="nearby-lesson__image-container"
                            :href="'/profile/' + lesson.instructor_id"
                        >
                            <img
                                class="nearby-lesson__image"
                                :src="lesson.instructor.profile.image"
                            />
                            <div class="nearby-lesson__upfront">
                                View more lessons
                            </div>
                        </a>
                        <h5 class="nearby-lesson__insta">
                            <a
                                :href="
                                    'https://www.instagram.com/' +
                                        lesson.instructor.profile
                                            .instagram_handle
                                "
                            >
                                @{{
                                    lesson.instructor.profile.instagram_handle
                                }}
                            </a>
                        </h5>
                        <a
                            class="nearby-lesson__name"
                            :href="'/profile/' + lesson.instructor_id"
                        >
                            {{ lesson.instructor.full_name }}
                        </a>
                    </div>
                    <div class="nearby-lesson__footer">
                        <div class="nearby-lesson__genre">
                            {{ lesson.genre.title }}
                        </div>
                        <span class="nearby-lesson__price"
                            >${{ lesson.spot_price }}</span
                        >
                        <span class="nearby-lesson__subprice">lesson</span>
                        <div class="nearby-lesson__date">
                            {{ formatDate(lesson.start, lesson.end) }}
                        </div>
                        <div class="nearby-lesson__location">
                            {{ lesson.city }}, {{ lesson.state }}
                        </div>
                        <loader-button
                            :isLoading="cartIsLoading"
                            :disabled="!canBook"
                            text="Book lesson"
                            @click="addToCart(lesson)"
                            class="nearby-lesson__book-lesson"
                        />
                    </div>
                </div>
            </div>
        </slick>

        <div class="upcoming-lessons-near-you__footer">
            <a href="/lessons" class="upcoming-lessons-near-you__view-all"
                >View All Lessons</a
            >
        </div>

        <info-popup ref="infoPopup" />

    </div>
</template>

<script>
import Slick from "vue-slick";
import InfoPopup from "../../instructor/InfoPopup/InfoPopup.vue";
import LoaderButton from "../../cart/LoaderButton/LoaderButton.vue";
import AnimLoader from "../../cart/AnimLoader/AnimLoader.vue";
import lessonService from "../../../services/lessonService";
import dateHelper from "../../../helpers/dateHelper";
import { mapActions } from "vuex";

export default {
    name: "UpcomingLessonsNearYou",
    components: {
        Slick,
        LoaderButton,
        AnimLoader,
        InfoPopup
    },
    props: {
        canBook: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            slickOptions: {
                infinite: false,
                dots: false,
                slidesToShow: 4,
                speed: 500,
                slidesToScroll: 4,
                responsive: [
                    {
                        breakpoint: 1300,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3
                        }
                    },
                    {
                        breakpoint: 1100,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    },
                    {
                        breakpoint: 650,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            },
            lessons: [],
            isLoading: true,
            cartIsLoading: false
        };
    },
    async mounted() {
        this.lessons = await lessonService.upcomingNearbyLessons();
        this.isLoading = false;
    },
    watch: {
        lessons() {
            if (this.$refs.upcomingLessonsNearYouSlick) {
                this.$refs.upcomingLessonsNearYouSlick.destroy();
                this.$nextTick(() => {
                    this.$refs.upcomingLessonsNearYouSlick.create();
                });
            }
        }
    },
    methods: {
        formatDate(startStr, endStr) {
            return dateHelper.formatDate(startStr, endStr);
        },
        formatMonth(month) {
            return dateHelper.threeLetMonthNameByNumber(month);
        },
        formatTime(hours, minutes) {
            return dateHelper.formatTime(hours, minutes);
        },
        ...mapActions(["addItemToCartAtStart"]),
        async addToCart(lesson) {
            this.cartIsLoading = true;
            const result = await this.addItemToCartAtStart({
                lessonId: lesson.id,
                specialRequest: ""
            });
            if (result.success) {
                this.$root.$emit("showMiniCart");
            } else {
                this.$refs.infoPopup.showInfo(result.message);
            }
            this.cartIsLoading = false;
        }
    }
};
</script>

<style lang="scss" scoped>
@import "./UpcomingLessonsNearYou.scss";
</style>
