<template>
  <div>
    <div class='mini-cart'>
      <transition name='show'>
        <div
          class='mini-cart__backdrop'
          @click='showMiniCart(false)'
          v-if='isMiniCartVisible'
        ></div>
      </transition>

      <transition name='slidein'>
        <div
          class='mini-cart__sidebar'
          @click.stop=''
          v-if='isMiniCartVisible'
        >
          <div class='mini-cart__header'>
            <h3 class='mini-cart__heading'>Cart</h3>
            <close-button
              class='mini-cart__close'
              @click='showMiniCart(false)'
            />
          </div>
          <anim-loader
            v-if='isLoading'
            v-scroll-lock='isMiniCartVisible'
          />
          <ul
            class='mini-cart__list'
            v-else-if='cartItems.length'
            v-scroll-lock='isMiniCartVisible'
          >
            <cart-item
              v-for='item in cartItems'
              :key='item.id'
              :item='item'
              @open-confirm='openConfirmPopup'
            />
          </ul>
          <div
            class='mini-cart__empty'
            v-else
            v-scroll-lock='isMiniCartVisible'
          >
            <span>No items in cart</span>
            <a
              href='/lessons' class='mini-cart__to-lessons'
            >Book lessons</a
            >
          </div>
          <div class='mini-cart__bottom'>
            <a
              class='mini-cart__checkout'
              href='/cart'
              v-if='cartItems.length'
            >Checkout</a
            >
            <button
              class='mini-cart__continue'
              @click.prevent='showMiniCart(false)'
            >
              Continue shopping
            </button>
          </div>
        </div>
      </transition>
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
import DeleteConfirmPopup from '../DeleteConfirmPopup.vue'
import CartItem from '../CartItem/CartItem.vue'
import AnimLoader from '../AnimLoader/AnimLoader.vue'
import CloseButton from '../../student/CloseButton/CloseButton.vue'

export default {
  name: 'MiniCart',
  components: {
    DeleteConfirmPopup,
    CartItem,
    AnimLoader,
    CloseButton
  },
  data() {
    return {
      isMiniCartVisible: false,
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
    }
  },
  watch: {
    isMiniCartVisible(newValue) {
      if (newValue) {
        this.fetchCartItems()
      }
    }
  },
  async mounted() {
    this.$root.$on('showMiniCart', () => this.showMiniCart())
  },
  methods: {
    ...mapActions({
      removeItemFromCart: 'removeItemFromCart',
      fetchCartTotal: 'fetchCartTotal',
      fetchCartItems: 'fetchCartItems'
    }),
    ...mapMutations({
      updateDotNeeded: 'updateDotNeeded'
    }),
    ...mapGetters({
      getAllCartItems: 'getAllCartItems',
      isCartLoading: 'isCartLoading'
    }),
    async deleteProduct() {
      await this.removeItemFromCart(this.selectedItemToDelete.id || this.selectedItemToDelete.lesson.id)
      this.closeConfirmPopup()
      await this.updateDotNeeded()
      await this.fetchCartTotal()
    },
    showMiniCart(show = true) {
      this.isMiniCartVisible = show
    },
    openConfirmPopup(item) {
      this.isDeletePopupOpen = true
      this.selectedItemToDelete = item
    },
    closeConfirmPopup() {
      this.isDeletePopupOpen = false
    }
  }
}
</script>

<style lang='scss' scoped>
@import './MiniCart.scss';
</style>
