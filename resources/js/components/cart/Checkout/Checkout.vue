<template>
  <div class="checkout">
    <div class="checkout__head">
      <h1 class="checkout__heading">Checkout</h1>
      <a class="checkout__continue-shopping" href="/lessons">Continue shopping</a>
    </div>
    <div class="checkout__row">
      <div class="checkout__column">
        <div class="user-info">
          <slot />
        </div>
      </div>
      <div class="checkout__column">
        <order-summary
          :is-loading="isTotalLoading"
          :number-of-lessons="getTotal.count"
          :subtotal="getTotal.subtotal"
          :total="getTotal.total"
          :skillective-fees="getTotal.fee"
          :discount="getTotal.discount"
          :show-checkout-button="false"
        />
      </div>
    </div>
  </div>
</template>

<script>
import OrderSummary from '../OrderSummary/OrderSummary.vue'
import AnimLoader from '../AnimLoader/AnimLoader.vue'
import { mapActions, mapGetters } from 'vuex'

export default {
  name: 'Checkout',
  components: {
    OrderSummary,
    AnimLoader,
  },
  async mounted() {
    await this.fetchCartTotal()
    this.isTotalLoading = false
  },
  data() {
    return {
      isTotalLoading: true,
      stepCount: 1,
    }
  },
  computed: {
    getTotal() {
      return this.getCartTotal()
    },
  },
  methods: {
    ...mapActions({
      fetchCartTotal: 'fetchCartTotal',
    }),
    ...mapGetters({
      getCartTotal: 'getCartTotal',
    }),
  },
}
</script>

<style lang="scss" scoped>
@import './Checkout.scss';
</style>
