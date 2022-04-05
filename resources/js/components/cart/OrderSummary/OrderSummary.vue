<template>
  <div class="order-summary">

    <h2 class="order-summary__heading">Order Summary</h2>

    <div class="order-summary__table">
      <div class="order-summary__row">
        <span class="order-summary__key">Number of lessons:</span>
        <span class="order-summary__spacer" />
        <span class="order-summary__value">{{ numberOfLessons }}</span>
      </div>
      <div class="order-summary__row">
        <span class="order-summary__key">Subtotal:</span>
        <span class="order-summary__spacer" />
        <span class="order-summary__value">${{ Number(subtotal).toFixed(2) }}</span>
      </div>
      <div class="order-summary__row">
        <span class="order-summary__key">Skillective Fees:</span>
        <span class="order-summary__spacer" />
        <span class="order-summary__value">${{ Number(skillectiveFees).toFixed(2) }}</span>
      </div>
      <div class="order-summary__row">
        <span class="order-summary__key">Total:</span>
        <span class="order-summary__spacer" />
        <span class="order-summary__value">${{ total }}</span>
      </div>
    </div>

    <div class="order-summary__bottom">
      <a class="order-summary__checkout" href="/student/checkout" v-if="showCheckoutButton">Checkout</a>
      <a class="order-summary__continue" href="/lessons">Continue shopping</a>
    </div>

  </div>
</template>

<script>

export default {
  name: "OrderSummary",
  props: {
    numberOfLessons: Number,
    subtotal: Number,
    skillectiveFees: Number,
    showCheckoutButton: Boolean,
  },
  computed: {
    total: function(){
      return (Number(this.subtotal) + Number(this.skillectiveFees)).toFixed(2)
    }
  }
}
</script>

<style lang="scss" scoped>
@import "./OrderSummary.scss";
</style>