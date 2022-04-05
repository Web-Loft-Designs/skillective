<template>
  <li class="cart-item">
    <div class="cart-item__header">
      <img
        :src="item.lesson.instructor.image"
        :alt="item.lesson.instructor.instagram_handle"
        class="cart-item__avatar"
      />
      <div class="cart-item__instructor">
        <span class="cart-item__insta">@{{ item.lesson.instructor.instagram_handle }}</span>
        <span class="cart-item__name">{{ item.lesson.instructor.full_name }}</span>
      </div>
      <close-button
        class="cart-item__remove"
        @click="removeItem(item.id)"
        title="Remove"
      />
    </div>

    <div class="cart-item__body">
      <div v-if="item.lesson.preview" class="cart-item__preview">
        <img :src="item.lesson.preview" alt="Lesson preview" />
      </div>
      <div class="cart-item__info">
        <span class="cart-item__title">{{
          getLessonTitle(item.lesson.title, item.lesson.genre)
        }}</span>
        <span class="cart-item__type"
          >Lesson Type: {{ getLessonType(item.lesson.lesson_type) }}</span
        >
        <span class="cart-item__date">
          {{ formatDate(item.lesson.start, item.lesson.end) }}
          {{ item.lesson.timezone_id }}
        </span>
        <span class="cart-item__note" v-if="note">
          <h4 class="cart-item__body-heading">Note:</h4>
          <read-more
            more-str="Read More"
            :text="note"
            less-str="Collapse"
            :max-chars="100"
            class="read-more-wrap"
          ></read-more>
        </span>
        <span class="cart-item__price">
          <span class="cart-item__price--number">
            ${{ item.lesson.spot_price || item.lesson.price }}
          </span>
          per lesson
        </span>
        <span class="cart-item__booking-request" v-if="bookingRequest">
          <h4 class="cart-item__body-heading">Booking request:</h4>
          {{ bookingRequest }}
        </span>
      </div>
    </div>
  </li>
</template>

<script>
import CloseButton from "../../student/CloseButton/CloseButton.vue";
import { mapActions, mapMutations } from "vuex";
import dateHelper from "../../../helpers/dateHelper";

export default {
  name: "CartItem",
  components: {
    CloseButton
  },
  props: {
    item: Object,
    note: String,
    bookingRequest: String,
  },
  methods: {
    ...mapActions({
      removeItemFromCart: "removeItemFromCart",
      fetchCartTotal: "fetchCartTotal",
    }),
    ...mapMutations({
      updateDotNeeded: "updateDotNeeded",
    }),
    getLessonTitle(title, genre) {
      let str = "";
      if (title) {
        str += title + " | ";
      }
      if (genre.title) {
        str += genre.title;
      } else {
        str += genre;
      }
      return str;
    },
    getLessonType(str) {
      switch (str) {
        case "virtual":
          return "Virtual";
        case "in_person":
          return "In Person";
        default:
          return "Pre-Recorded";
      }
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
    async removeItem(id) {
      await this.removeItemFromCart(id);
      this.updateDotNeeded();
      this.fetchCartTotal();
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./CartItem.scss";
</style>