<template>
  <li class="cart-item">
    <div class="cart-item__header">
      <img
        :src="item.lesson.instructor.image"
        :alt="item.lesson.instructor.instagram_handle"
        class="cart-item__avatar"
      />
      <div class="cart-item__instructor">
        <span class="cart-item__insta"
          >@{{ item.lesson.instructor.instagram_handle }}</span
        >
        <span class="cart-item__name">{{
          item.lesson.instructor.full_name
        }}</span>
      </div>
      <close-button
        class="cart-item__remove"
        @click="removeItem(item)"
        title="Remove"
      />
    </div>

    <div class="cart-item__discount">
      <div
        v-for="(discount, discountIndex) in item.discounts"
        :key="discountIndex"
        class="cart-item__discount-inner"
      >
        <h6>{{ discount.title }}</h6>
        <ul v-if="discount.isActivate">
          <li>
            <span>{{ generateDiscountAmount(discount) }} discount </span> is
            applied.
          </li>
        </ul>
        <ul v-else>
          <li>
            If you buy more than:
            <b> {{ generateDiscountTitle(discount) }} </b> from this instructor,
            you will receive an
            <span>{{ generateDiscountAmount(discount) }} discount.</span>
          </li>
        </ul>
      </div>

      <a href="/lessons">Continue Shopping</a>
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
import ContentViewer from "../../profile/ContentViewer/ContentViewer.vue";

export default {
  name: "CartItem",
  components: {
    CloseButton,
    ContentViewer,
  },
  props: {
    item: {
      type: Object,
      default: () => {
        return {};
      },
    },
    note: {
      type: String,
      default: "",
    },
    bookingRequest: {
      type: String,
      default: "",
    },
  },
  methods: {
    ...mapActions({
      removeItemFromCart: "removeItemFromCart",
      fetchCartTotal: "fetchCartTotal",
    }),
    ...mapMutations({
      updateDotNeeded: "updateDotNeeded",
    }),
    generateDiscountAmount(discount) {
      if (discount.discount_type === "percent") {
        return `${discount.discount}%`;
      } else if (discount.discount_type === "fixed-amount") {
        return `$${discount.discount}`;
      }
    },
    generateDiscountTitle(discount) {
      if (discount.lesson_type === "all") {
        return `${discount.itemsLeft} Any Lessons`;
      } else if (discount.lesson_type === "virtual") {
        return `${discount.itemsLeft} Virtual Lessons`;
      } else if (discount.lesson_type === "pre-recorded") {
        return `${discount.itemsLeft} Pre-Recorded Lessons`;
      } else if (discount.lesson_type === "in-person") {
        return `${discount.itemsLeft} In-Person Lessons`;
      }
    },
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
        case "in_person_client":
          return "In Person";
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
    async removeItem(item) {
      await this.removeItemFromCart(item.id || item.lesson.id);
      this.updateDotNeeded();
      this.fetchCartTotal();
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./CartItem.scss";
</style>
