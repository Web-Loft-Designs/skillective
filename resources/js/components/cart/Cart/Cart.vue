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
              v-for="(item) in cartItems"
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
          :numberOfLessons="getTotal.count"
          :subtotal="getTotal.total"
          :skillectiveFees="getTotal.fee"
          :showCheckoutButton="cartItems.length > 0"
        />
      </div>

    </div>
  </div>
</template>

<script>
import OrderSummary from "../OrderSummary/OrderSummary.vue";
import CartItem from "../CartItem/CartItem.vue";
import AnimLoader from "../AnimLoader/AnimLoader.vue";
import { mapActions, mapMutations, mapGetters } from 'vuex';

export default {
  name: "Cart",
  components: {
    OrderSummary,
    CartItem,
    AnimLoader,
  },
  props: {
    total: Object,
  },
  mounted() {
    this.fetchCartItems();
    this.updateCartTotal(this.total);
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
      fetchCartItems: 'fetchCartItems',
    }),
    ...mapGetters({
      getAllCartItems: 'getAllCartItems',
      isCartLoading: 'isCartLoading',
      getCartTotal: 'getCartTotal',
    }),
    ...mapMutations({
      updateCartTotal: 'updateCartTotal',
    }),
  },
}
</script>

<style lang="scss" scoped>
@import "./Cart.scss";
</style>