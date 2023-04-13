<template>
  <li class='cart-item'>
    <div class='cart-item__header'>
      <img
        :alt='item.lesson.instructor.instagram_handle'
        :src='item.lesson.instructor.image'
        class='cart-item__avatar'
      />
      <div class='cart-item__instructor'>
        <span class='cart-item__name'>
          {{ item.lesson.instructor.full_name }}
        </span>
        <span class='cart-item__insta'>
          @{{ item.lesson.instructor.instagram_handle }}
        </span>
      </div>
      <close-button
        class='cart-item__remove'
        title='Remove'
        @click='removeItem(item)'
      />
    </div>

    <div
      v-if='item.discounts.length'
      class='cart-item__discount'
    >
      <div
        v-for='(discount, discountIndex) in item.discounts'
        :key='discountIndex'
        class='cart-item__discount-inner'
      >
        <h6>{{ discount.title }}</h6>
        <ul v-if='discount.isActivate'>
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

      <a href='/lessons'>Continue Shopping</a>
    </div>

    <div class='cart-item__body'>
      <div
        v-if='item.lesson.preview'
        class='cart-item__preview'
      >
        <img
          :src='item.lesson.preview'
          alt='Lesson preview'
        />
      </div>
      <div class='cart-item__info'>
        <span class='cart-item__title'>
          {{ getLessonTitle(item.lesson.title, item.lesson.genre) }}
        </span>
        <span class='cart-item__type'>
          Lesson Type: {{ getLessonType(item.lesson.lesson_type) }}
        </span>
        <span class='cart-item__date'>
          {{ formatDate(item.lesson.start, item.lesson.end) }}
          {{ item.lesson.timezone_id }}
        </span>
        <span
          v-if='note'
          class='cart-item__note'
        >
          <h4 class='cart-item__body-heading'>Note:</h4>
          <read-more
            :max-chars='100'
            :text='note'
            class='read-more-wrap'
            less-str='Collapse'
            more-str='Read More'
          ></read-more>
        </span>
        <span class='cart-item__price'>
          <span class='cart-item__price--number'>
            ${{ productPrice(item.lesson.spot_price || item.lesson.price) }}
          </span>
          per lesson
        </span>
        <span
          v-if='bookingRequest'
          class='cart-item__booking-request'
        >
          <h4 class='cart-item__body-heading'>Booking request:</h4>
          {{ bookingRequest }}
        </span>
      </div>
    </div>
  </li>
</template>

<script>
import CloseButton from '../../student/CloseButton/CloseButton.vue'
import dateHelper from '../../../helpers/dateHelper'
import ContentViewer from '../../profile/ContentViewer/ContentViewer.vue'

export default {
  name: 'CartItem',
  components: {
    CloseButton,
    ContentViewer
  },
  props: {
    item: {
      type: Object,
      default: () => ({})
    },
    note: {
      type: String,
      default: ''
    },
    bookingRequest: {
      type: String,
      default: ''
    }
  },
  methods: {
    productPrice(value) {
      return typeof value === 'string' && value.endsWith('00') ? parseFloat(value.substring(0, value.length - 2)) : value
    },
    generateDiscountAmount(discount) {
      if (discount.discount_type === 'percent') {
        return `${ discount.discount }%`
      } else if (discount.discount_type === 'fixed-amount') {
        return `$${ discount.discount }`
      }
    },
    generateDiscountTitle(discount) {
      if (discount.lesson_type === 'all') {
        return `${ discount.itemsLeft } Any Lessons`
      } else if (discount.lesson_type === 'virtual') {
        return `${ discount.itemsLeft } Virtual Lessons`
      } else if (discount.lesson_type === 'pre-recorded') {
        return `${ discount.itemsLeft } Pre-Recorded Lessons`
      } else if (discount.lesson_type === 'in-person') {
        return `${ discount.itemsLeft } In-Person Lessons`
      }
    },
    getLessonTitle(title, genre) {
      let str = ''
      if (title) {
        str += title + ' | '
      }
      if (genre.title) {
        str += genre.title
      } else {
        str += genre
      }
      return str
    },
    getLessonType(str) {
      switch (str) {
        case 'virtual':
          return 'Virtual'
        case 'in_person_client':
          return 'In Person'
        case 'in_person':
          return 'In Person'
        default:
          return 'Pre-Recorded'
      }
    },
    formatDate(startStr, endStr) {
      return dateHelper.formatDate(startStr, endStr)
    },
    formatMonth(month) {
      return dateHelper.threeLetMonthNameByNumber(month)
    },
    formatTime(hours, minutes) {
      return dateHelper.formatTime(hours, minutes)
    },
    removeItem(item) {
      this.$emit('open-confirm', item)
    }
  }
}
</script>

<style lang='scss' scoped>
@import './CartItem.scss';
</style>
