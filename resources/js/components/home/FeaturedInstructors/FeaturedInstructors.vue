<template>
    <div class="featured-instructors">
        <h2 class="featured-instructors__heading">Featured Instructors</h2>
        <anim-loader v-if="isLoading" />
        <slick v-else :options="slickOptions" ref="featuredInstructorsSlick">
            <div
                class="featured-instructor__outer"
                v-for="(instructor, instructorIndex) in instructors"
                :key="instructorIndex"
            >
                <a
                    :href="'/profile/' + instructor.id"
                    class="featured-instructor"
                >
                    <img
                        class="featured-instructor__image"
                        :src="instructor.profile.image"
                    />
                    <h5 class="featured-instructor__name">
                        {{ instructor.full_name }}
                    </h5>
                    <span class="featured-instructor__insta"
                        >@{{ instructor.profile.instagram_handle }}</span
                    >
                    <div class="featured-instructor__genres">
                        <span
                            class="featured-instructor__genre"
                            v-for="(genre, genreIndex) in instructor.genres"
                            :key="genreIndex"
                            >{{ genre.title }}</span
                        >
                    </div>
                </a>
            </div>
        </slick>
    </div>
</template>

<script>
import Slick from "vue-slick";
import instructorService from "../../../services/instructorService";
import AnimLoader from "../../cart/AnimLoader/AnimLoader.vue";

export default {
    name: "FeaturedInstructors",
    components: {
        Slick,
        AnimLoader,
    },
    data() {
        return {
            slickOptions: {
                infinite: false,
                rows: 2,
                dots: true,
                slidesToShow: 5,
                speed: 500,
                slidesToScroll: 5,
                responsive: [
                    {
                        breakpoint: 1300,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3
                        }
                    },
                    {
                        breakpoint: 900,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    }
                ]
            },
            instructors: [],
            isLoading: true,
        };
    },
    async mounted() {
        let instructors = await instructorService.featuredInstructors();
        instructors.forEach(value => {
            if (value.genres.length > 2) {
                value.genres = [
                    value.genres[0],
                    value.genres[1],
                    { title: value.genres.length - 2 + "+" }
                ];
            }
        });
        this.instructors = instructors;
        this.isLoading = false;
    },
    watch: {
        instructors() {
            if (this.$refs.featuredInstructorsSlick) {
                this.$refs.featuredInstructorsSlick.destroy();
                this.$nextTick(() => {
                    this.$refs.featuredInstructorsSlick.create();
                });
            }
        }
    }
};
</script>

<style lang="scss" scoped>
@import "./FeaturedInstructors.scss";
</style>
