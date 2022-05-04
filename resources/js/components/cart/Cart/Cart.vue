<template>
  <div class="cart">
    <h1 class="cart__heading">Cart</h1>
    <div class="cart__row">
      <div class="cart__column">
        <div class="cart__purchases">
          <h2 class="cart__subheading">All Purchases</h2>

          <anim-loader v-if="isLoading" />

          <ul class="cart__list" v-else-if="cartItems.length">
            <cart-item
              v-for="item in cartItems"
              :key="item.id"
              :item="item"
              :note="item.lesson.description"
              :bookingRequest="item.description"
            />
          </ul>

          <div class="cart__empty" v-else>
            <span>No items in cart</span>
          </div>
        </div>
      </div>

      <div class="cart__column">
        <order-summary
          :is-loading="isTotalLoading"
          :number-of-lessons="getTotal.count"
          :subtotal="getTotal.subtotal"
          :total="getTotal.total"
          :discount="getTotal.discount"
          :skillective-fees="getTotal.fee"
          :show-checkout-button="cartItems.length > 0"
          :show-apply-promo-button="cartItems.length > 0"
          @apply-promo="applyPromo()"
        />
        <promo-summary v-if="promos.length" v-model="promos" />
      </div>
    </div>
  </div>
</template>

<script>
import OrderSummary from "../OrderSummary/OrderSummary.vue";
import PromoSummary from "../PromoSummary/PromoSummary.vue";
import CartItem from "../CartItem/CartItem.vue";
import AnimLoader from "../AnimLoader/AnimLoader.vue";
import guestCartHelper from "../../../helpers/guestCartHelper";

import { mapActions, mapGetters } from "vuex";

export default {
  name: "Cart",
  components: {
    OrderSummary,
    PromoSummary,
    CartItem,
    AnimLoader,
  },
  async mounted() {
    guestCartHelper.clearPromos();

    this.fetchCartItems();
    await this.fetchCartTotal();
    this.isTotalLoading = false;
  },
  data() {
    return {
      isTotalLoading: true,
      promos: [],
    };
  },
  computed: {
    cartItems() {
      return this.getAllCartItems();
    },
    isLoading() {
      return this.isCartLoading();
    },
    getTotal() {
      return this.getCartTotal();
    },
  },
  methods: {
    ...mapActions({
      fetchCartItems: "fetchCartItems",
      fetchCartTotal: "fetchCartTotal",
    }),
    ...mapGetters({
      getAllCartItems: "getAllCartItems",
      isCartLoading: "isCartLoading",
      getCartTotal: "getCartTotal",
    }),
    applyPromo(promo = "", active = false) {
      this.promos.push({
        promo,
        active,
      });
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./Cart.scss";
</style>
