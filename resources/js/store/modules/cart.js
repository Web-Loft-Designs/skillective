import { initialState } from '../initialState'
import cartService from '../../services/cartService'

export default {
  state: initialState,
  actions: {
    async fetchCartItems(context) {
      context.commit('setIsCartLoading', true)
      const items = await cartService.fetchCartItems(
        context.state.guestMode
      )
      await context.commit('updateAllCartItems', items)
      context.commit('setIsCartLoading', false)
      context.commit('updateDotNeeded')
    },
    async addItemToCartAtStart(context, lessonData) {
      context.commit('setIsCartLoading', true)
      const newItem = await cartService.addItemToCartAtStart(
        lessonData,
        context.state.guestMode
      )
      context.commit('setIsCartLoading', false)
      return newItem
    },
    async removeItemFromCart(context, id) {
      await cartService.removeItemFromCart(id, context.state.guestMode)
      await context.commit('removeItemFromCart', id)
      context.commit('updateDotNeeded')
    },
    async fetchDotNeeded(context) {
      const needed = await cartService.checkCartDotNeeded(
        context.state.guestMode
      )
      context.commit('updateDotNeeded', needed)
    },
    async fetchCartTotal(context) {
      const total = await cartService.fetchCartTotal(
        context.state.guestMode
      )
      context.commit('updateCartTotal', total)
    }
  },
  mutations: {
    async setIsCartLoading(state, bool) {
      state.cart.isLoading = bool
    },
    async updateAllCartItems(state, items) {
      state.cart.items = items
    },
    async removeItemFromCart(state, id) {
      state.cart.items = state.cart.items.filter(
        item => (item.id || item.lesson.id) !== id
      )
    },
    async updateDotNeeded(state, visible = null) {
      state.cart.dotNeeded = visible || state.cart.items.length > 0
    },
    async updateCartTotal(state, total) {
      state.cart.total = total
    },
    async setGuestMode(state, guestMode) {
      state.guestMode = guestMode
    }
  },
  getters: {
    getAllCartItems(state) {
      return state.cart.items
    },
    isCartLoading(state) {
      return state.cart.isLoading
    },
    getCartTotal(state) {
      return state.cart.total
    },
    isDotNeeded(state) {
      return state.cart.dotNeeded
    }
  }
}
