<template>
  <div>
    <div class='cart'>
      <h1 class='cart__heading'>Cart</h1>
      <div class='cart__row'>
        <div class='cart__column'>
          <div class='cart__purchases'>
            <h2 class='cart__subheading'>All Purchases</h2>

            <anim-loader v-if='isLoading'/>

            <ul class='cart__list' v-else-if='cartItems.length'>
              <cart-item
                v-for='item in cartItems'
                :key='item.id'
                :item='item'
                :note='item.lesson.description'
                :bookingRequest='item.description'
                @open-confirm='openConfirmPopup'
              />
            </ul>

            <div class='cart__empty' v-else>
              <span>No items in cart</span>
            </div>
          </div>
        </div>

        <div class='cart__column'>
          <order-summary
            :is-loading='isTotalLoading'
            :number-of-lessons='getTotal.count'
            :subtotal='getTotal.subtotal'
            :total='getTotal.total'
            :discount='getTotal.discount'
            :skillective-fees='getTotal.fee'
            :show-checkout-button='cartItems.length > 0'
            :show-apply-promo-button='cartItems.length > 0'
            @apply-promo='applyPromo()'
          />
          <promo-summary v-if='promos.length' v-model='promos'/>
        </div>
      </div>
    </div>
    <delete-confirm-popup
      :is-open='isDeletePopupOpen'
      @delete='deleteProduct'
      @close-popup='closeConfirmPopup'
    />
  </div>
</template>

<script>
import { mapActions, mapGetters, mapMutations } from 'vuex'
import OrderSummary from '../OrderSummary/OrderSummary.vue'
import PromoSummary from '../PromoSummary/PromoSummary.vue'
import CartItem from '../CartItem/CartItem.vue'
import AnimLoader from '../AnimLoader/AnimLoader.vue'
import guestCartHelper from '../../../helpers/guestCartHelper'
import DeleteConfirmPopup from '../DeleteConfirmPopup.vue'

export default {
  name: 'Cart',
  components: {
    DeleteConfirmPopup,
    OrderSummary,
    PromoSummary,
    CartItem,
    AnimLoader
  },
  data() {
    return {
      isTotalLoading: true,
      promos: [],
      isDeletePopupOpen: false,
      selectedItemToDelete: null
    }
  },
  computed: {
    cartItems() {
      return this.getAllCartItems()
    },
    isLoading() {
      return this.isCartLoading()
    },
    getTotal() {
      return this.getCartTotal()
    }
  },
  async mounted() {
    guestCartHelper.clearPromos()
    await this.fetchCartItems()
    await this.fetchCartTotal()
    this.isTotalLoading = false
  },
  methods: {
    ...mapActions({
      fetchCartItems: 'fetchCartItems',
      fetchCartTotal: 'fetchCartTotal',
      removeItemFromCart: 'removeItemFromCart'
    }),
    ...mapMutations({
      updateDotNeeded: 'updateDotNeeded'
    }),
    ...mapGetters({
      getAllCartItems: 'getAllCartItems',
      isCartLoading: 'isCartLoading',
      getCartTotal: 'getCartTotal'
    }),
    async deleteProduct() {
      await this.removeItemFromCart(this.selectedItemToDelete.id || this.selectedItemToDelete.lesson.id)
      this.closeConfirmPopup()
      await this.updateDotNeeded()
      await this.fetchCartTotal()
    },
    openConfirmPopup(item) {
      this.isDeletePopupOpen = true
      this.selectedItemToDelete = item
    },
    closeConfirmPopup() {
      this.isDeletePopupOpen = false
    },
    applyPromo(promo = '', active = false) {
      this.promos.push({
        promo,
        active
      })
    }
  }
}
</script>

<style lang='scss' scoped>
@import './Cart.scss';
</style>
