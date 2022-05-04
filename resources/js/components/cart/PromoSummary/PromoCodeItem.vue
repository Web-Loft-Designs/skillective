<template>
  <div class="promo-summary__wrap">
    <li
      :class="{
        'promo-summary__item': true,
        'promo-summary__item--active': active,
        'promo-summary__item--error': error,
      }"
    >
      <input
        type="text"
        @keyup="promoOnChange"
        v-model="promo"
        placeholder="Enter Promo Code"
      />
      <anim-loader v-if="isFetching" />
    </li>
    <div v-if="error" class="promo-summary__item-error">
      <span> {{ error }}</span>
    </div>
    <div v-if="active" class="promo-summary__item-success">
      <span> Promo Code Applied </span>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import guestCartHelper from "../../../helpers/guestCartHelper";
import { mapActions } from "vuex";
import AnimLoader from "../AnimLoader/AnimLoader.vue";

export default {
  name: "PromoCodeItem",
  props: ["value"],
  data() {
    return {
      promo: this.value.promo,
      timer: null,
      isFetching: false,
      active: this.value.active,
      error: "",
    };
  },
  components: {
    AnimLoader,
  },
  methods: {
    ...mapActions({
      fetchCartItems: "fetchCartItems",
      fetchCartTotal: "fetchCartTotal",
    }),
    promoOnChange(e) {
      if (this.timer) {
        clearTimeout(this.timer);
        this.timer = null;
      }
      this.timer = setTimeout(() => {
        this.checkPromo(e);
      }, 500);
    },
    checkPromo({ target }) {
      if (!target.value) {
        return;
      }

      if (this.active) {
        return;
      }

      if (this.timer) {
        clearTimeout(this.timer);
        this.timer = null;
      }

      this.isFetching = true;
      axios
        .get(`/api/cart/promo/${target.value}`)
        .then((response) => {
          if (response.data.success) {
            const result = guestCartHelper.addPromos(target.value);

            if (!result.success) {
              this.error = result.message;
              this.isFetching = false;
              return;
            }

            this.fetchCartTotal();
            this.active = true;
          }
          this.isFetching = false;
        })
        .catch((error) => {
          this.error = error.response.data.message;
          this.isFetching = false;
        });
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./PromoSummary.scss";
</style>