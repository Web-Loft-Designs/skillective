<template>
  <div class="discount-management">
    <div class="discount-management__container">
      <div class="discount-management__sidebar">
        <h1 class="discount-management__title">Discount Management</h1>
        <ul class="discount-management__tabs">
          <li v-for="(tab, tabIndex) in tabs" :key="tabIndex">
            <button @click="activeTab = tab.code" :class="{
              'discount-management__tab': true,
              'discount-management__tab--active': activeTab == tab.code,
            }">{{ tab.title }}</button>
          </li>
        </ul>
      </div>
      <div class="discount-management__content">
        
        <div class="discount-management__content-tab" v-if="activeTab == 'discount'">
          <discounts-editor />
        </div>

        <div class="discount-management__content-tab" v-if="activeTab == 'promo'">
          <promos-editor />
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import DiscountsEditor from "../DiscountsEditor/DiscountsEditor.vue";
import PromosEditor from "../PromosEditor/PromosEditor.vue";

export default {
  name: "DiscountManagement",
  components: {
    DiscountsEditor,
    PromosEditor,
  },
  data() {
    return {
      activeTab: "discount",
      tabs: [
        {
          code: "discount",
          title: "Discount",
        },
        {
          code: "promo",
          title: "Promo code",
        },
      ],
    }
  },
};
</script>

<style lang="scss" scoped>
@import "./DiscountManagement.scss";
</style>