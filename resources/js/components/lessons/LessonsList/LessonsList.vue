<template>
    <div class="lessons-list">
        <div class="lessons-list__list" v-if="lessons.length">
            <div
                v-for="(lesson, lessonIndex) in lessons"
                :key="lessonIndex"
                :class="{
                    lesson: true,
                    'lesson--active': activeLesson && lesson.id == activeLesson
                }"
                @mouseover="activeLesson = lesson.id"
                @click.stop="activeLesson = lesson.id"
                v-click-outside="clearActiveCard"
                :data-id="lesson.id"
            >
                <div class="lesson__body">
                    <a
                        class="lesson__image-container"
                        :href="'/profile/' + lesson.instructor_id"
                    >
                        <img
                            class="lesson__image"
                            :src="lesson.instructor.profile.image"
                        />
                        <div class="lesson__upfront">View more lessons</div>
                    </a>
                    <h5
                        v-if="
                            lesson.instructor &&
                                lesson.instructor.profile &&
                                lesson.instructor.profile.instagram_handle
                        "
                        class="lesson__insta"
                    >
                        <a
                            :href="
                                'https://www.instagram.com/' +
                                    lesson.instructor.profile.instagram_handle
                            "
                            target="_blank"
                            >@{{
                                lesson.instructor.profile.instagram_handle
                            }}</a
                        >
                    </h5>
                    <a
                        v-if="lesson.instructor && lesson.instructor.full_name"
                        :href="'/profile/' + lesson.instructor_id"
                        class="lesson__name"
                        >{{ lesson.instructor.full_name }}</a
                    >
                </div>
                <div class="lesson__footer">
                    <div class="lesson__footer-head">
                        <div class="lesson__genre">
                            {{ lesson.genre.title }}
                        </div>
                        <span class="lesson__price">
                            ${{ lesson.spot_price }}
                            <span>lesson</span>
                        </span>
                    </div>
                    <div v-if="lesson.title" class="lesson__date">
                        {{ lesson.title }}
                    </div>
                    <div v-if="lesson.start && lesson.end" class="lesson__date">
                        {{ formatDate(lesson.start, lesson.end) }}
                    </div>
                    <div
                        v-if="lesson.city && lesson.state"
                        class="lesson__location"
                    >
                        {{ lesson.city }}, {{ lesson.state }}
                    </div>
                    <loader-button
                        class="lesson__button"
                        :disabled="!canBook"
                        :isLoading="cartIsLoading"
                        text="Add to cart"
                        @click="addToCart(lesson.id)"
                    />
                </div>
            </div>
        </div>

        <span v-else class="lessons-list__empty"
            >There are currently no instructors teaching lessons in your area.</span
        >

        <info-popup ref="infoPopup" />
    </div>
</template>

<script>
import dateHelper from "../../../helpers/dateHelper";
import { mapActions } from "vuex";
import ClickOutside from "vue-click-outside";
import LoaderButton from "../../cart/LoaderButton/LoaderButton.vue";
import InfoPopup from "../../instructor/InfoPopup/InfoPopup.vue";

export default {
    name: "LessonsList",
    directives: {
        ClickOutside
    },
    components: {
        LoaderButton,
        InfoPopup
    },
    model: {
        prop: "activeLesson"
    },
    props: {
        lessons: {
            type: Array,
            default: () => {
                return [];
            }
        },
        canBook: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            activeLesson: null,
            cartIsLoading: false
        };
    },
    watch: {
        activeLesson(newValue) {
            this.$emit("input", newValue);
        }
    },
    methods: {
        ...mapActions(["addItemToCartAtStart"]),
        async addToCart(lessonId) {
            this.cartIsLoading = true;
            const result = await this.addItemToCartAtStart({
                lessonId,
                specialRequest: ""
            });
            if (result.success || (result.data && result.data.success)) {
                this.$root.$emit("showMiniCart");
            } else {
                this.$refs.infoPopup.showInfo(result.message);
            }
            this.cartIsLoading = false;
        },
        formatDate(startStr, endStr) {
            return dateHelper.formatDate(startStr, endStr);
        },
        formatMonth(month) {
            return dateHelper.threeLetMonthNameByNumber(month);
        },
        formatTime(hours, minutes) {
            return dateHelper.formatTime(hours, minutes);
        },
        clearActiveCard() {
            this.activeLesson = null;
        }
    }
};
</script>

<style lang="scss" scoped>
@import "./LessonsList.scss";
</style>
