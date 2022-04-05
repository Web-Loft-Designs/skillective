import { initialState } from "../initialState";
import cartService from "../../services/cartService";

export default {
  state: initialState,
  actions: {
    async fetchCartItems(context) {
      context.commit('setIsCartLoading', true);
      const items = await cartService.fetchCartItems();
      await context.commit('updateAllCartItems', items);
      context.commit('setIsCartLoading', false);
      context.commit('updateDotNeeded');
    },
    async addItemToCartAtStart(context, lessonId, specialRequest) {
      context.commit('setIsCartLoading', true);
      const newItem = await cartService.addItemToCartAtStart(lessonId, specialRequest);
      context.commit('setIsCartLoading', false);
      return newItem;
    },
    async removeItemFromCart(context, id) {
      await cartService.removeItemFromCart(id);
      await context.commit('removeItemFromCart', id);
      context.commit('updateDotNeeded');
    },
    async fetchDotNeeded(context) {
      const needed = await cartService.checkCartDotNeeded();
      context.commit('updateDotNeeded', needed.data.data);
    },
    async fetchCartTotal(context) {
      const total = await cartService.fetchCartTotal();
      context.commit('updateCartTotal', total);
    },
  },
  mutations: {
    async setIsCartLoading(state, bool) {
      state.cart.isLoading = bool;
    },
    async updateAllCartItems(state, items) {
      state.cart.items = items;
    },
    async removeItemFromCart(state, id) {
      state.cart.items = state.cart.items.filter(item => item.id != id);
    },
    async updateDotNeeded(state, visible = null) {
      state.cart.dotNeeded = visible || state.cart.items.length > 0;
    },
    async updateCartTotal(state, total) {
      state.cart.total = total;
    },
  },
  getters: {
    getAllCartItems(state) {
      return state.cart.items;
    },
    isCartLoading(state) {
      return state.cart.isLoading;
    },
    getCartTotal(state) {
      return state.cart.total;
    },
    isDotNeeded(state) {
      return state.cart.dotNeeded;
    }
  }
};