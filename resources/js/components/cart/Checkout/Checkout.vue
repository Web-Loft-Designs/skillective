<template>
  <div class="checkout">
    <h1 class="checkout__heading">Checkout</h1>
     <div class="checkout__row">
      <div class="checkout__column">
        <div class="user-info">

          <slot />

          <!--<h2 class="user-info__heading">User Information</h2>

          <div class="user-info__input-info">
            <span class="user-info__input-info--title">Input information or</span>
            <a class="user-info__input-info--link" href="/">Input information or</a>
          </div>

          <h4>Complete name</h4>
          <div class="user-info__flex">
            <input type="text" class="user-info__first-name" placeholder="First Name" />
            <input type="text" class="user-info__last-name" placeholder="Last Name" />
          </div>

          <h4>Instagram Handle</h4>
          <input type="text" class="user-info__insta" placeholder="@instagram_name" />

          <div class="user-info__flex">
            <div class="user-info__column">
              <h4>City</h4>
              <input type="text" class="user-info__city" placeholder="City" />
            </div>
            <div class="user-info__column">
              <h4>State</h4>
              <input type="text" class="user-info__state" placeholder="State" />
            </div>
            <div class="user-info__column">
              <h4>ZIP</h4>
              <input type="text" class="user-info__zip" placeholder="ZIP Code" />
            </div>
          </div>-->

        </div>
      </div>
      <div class="checkout__column">
        <order-summary 
          :numberOfLessons="getTotal.count"
          :subtotal="getTotal.total"
          :skillectiveFees="getTotal.fee"
          :showCheckoutButton="false"
        />
      </div>
    </div>
  </div>
</template>

<script>
import OrderSummary from "../OrderSummary/OrderSummary.vue";
import AnimLoader from "../AnimLoader/AnimLoader.vue";
import { mapGetters, mapMutations } from 'vuex';

export default {
  name: "Checkout",
  components: {
    OrderSummary,
    AnimLoader,
  },
  props: {
    total: Object,
  },
  mounted() {
    this.updateCartTotal(this.total);
  },
  computed: {
    getTotal() {
      return this.getCartTotal();
    },
  },
  methods: {
    ...mapGetters({
      getCartTotal: 'getCartTotal',
    }),
    ...mapMutations({
      updateCartTotal: 'updateCartTotal',
    }),
  },
}
</script>

<style lang="scss" scoped>
@import "./Checkout.scss";
</style>