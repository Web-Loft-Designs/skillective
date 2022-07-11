<template>
  <div class="order-summary">
    <h2 class="order-summary__heading">Order Summary</h2>

    <anim-loader v-if="isLoading" />

    <div v-else class="order-summary__table">
      <div class="order-summary__row">
        <span class="order-summary__key">Number of lessons:</span>
        <span class="order-summary__spacer" />
        <span class="order-summary__value">{{ numberOfLessons }}</span>
      </div>
      <div class="order-summary__row">
        <span class="order-summary__key">Subtotal:</span>
        <span class="order-summary__spacer" />
        <span class="order-summary__value"
          >${{ Number(subtotal).toFixed(2) }}</span
        >
      </div>
      <div class="order-summary__row">
        <span class="order-summary__key">Discount:</span>
        <span class="order-summary__spacer" />
        <span class="order-summary__value"
          >${{ Number(discount).toFixed(2) }}</span
        >
      </div>
      <div class="order-summary__row">
        <span class="order-summary__key">Skillective Fees:</span>
        <span class="order-summary__spacer" />
        <span class="order-summary__value"
          >${{ Number(skillectiveFees).toFixed(2) }}</span
        >
      </div>
      <div class="order-summary__row">
        <span class="order-summary__key">Total:</span>
        <span class="order-summary__spacer" />
        <span class="order-summary__value">${{ Number(total).toFixed(2) }}</span>
      </div>
    </div>

    <div class="order-summary__bottom">
      <button
        class="order-summary__apply-promo"
        v-if="showApplyPromoButton"
        @click="emitApplyPromo()"
      >
        Apply Promo Code
      </button>
      <a
        class="order-summary__checkout"
        href="/checkout"
        v-if="showCheckoutButton"
        >Checkout</a
      >
      <a class="order-summary__continue" href="/lessons">Continue shopping</a>
    </div>
  </div>
</template>

<script>
import AnimLoader from "../AnimLoader/AnimLoader.vue";

export default {
  name: "OrderSummary",
  components: {
    AnimLoader,
  },
  props: {
    isLoading: {
      type: Boolean,
      default: false,
    },
    numberOfLessons: {
      type: Number,
      default: 0,
    },
    subtotal: {
      type: Number,
      default: 0,
    },
    total: {
      type: Number,
      default: 0,
    },
    discount: {
      type: Number,
      default: 0,
    },
    skillectiveFees: {
      type: Number,
      default: 0,
    },
    showCheckoutButton: {
      type: Boolean,
      default: false,
    },
    showApplyPromoButton: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    emitApplyPromo() {
      this.$emit("apply-promo");
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./OrderSummary.scss";
</style>
