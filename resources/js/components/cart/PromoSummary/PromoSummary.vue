<template>
  <div class="promo-summary">
    <h2 class="promo-summary__heading">Promo code</h2>

    <div class="promo-summary__body">
      <span class="promo-summary__text"
        >Each promo code is valid only for a specific instructor.</span
      >
      <ul class="promo-summary__list">
        <promo-code-item
          v-for="(promo, promoIndex) in promos"
          :key="promoIndex"
          :value="promo.promo"
        >
        </promo-code-item>
      </ul>
      <button @click="addPromoCode()" class="promo-summary__add">
        + Add another promo code
      </button>
    </div>
  </div>
</template>

<script>

import PromoCodeItem from "./PromoCodeItem.vue";
import { mapActions } from "vuex";

export default {
  components: { PromoCodeItem },
  name: "PromoSummary",
  props: {
    value: {
      type: Array,
      default: () => {
        return [];
      },
    },
  },
  data() {
    return {
      promos: this.value,
      timer: null,
    };
  },
  methods: {
    ...mapActions({
      fetchCartItems: "fetchCartItems",
      fetchCartTotal: "fetchCartTotal",
    }),
    addPromoCode(promo = "") {
      this.promos.push({
        promo,
        active: false,
      });
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./PromoSummary.scss";
</style>