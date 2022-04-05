import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

// import { initialState } from "./initialState";
import cart from "./modules/cart";

const store = new Vuex.Store({
  // state: initialState,
  modules: {
    cart
  }
});

export default store;

