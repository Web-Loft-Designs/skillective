<template>
  <div class='discount-management'>
    <div class='discount-management__container'>
      <div class='discount-management__sidebar'>
        <h1 class='discount-management__title'>Discount Management</h1>
        <ul class='discount-management__tabs'>
          <li
            v-for='(tab, tabIndex) in tabs'
            :key='tabIndex'
          >
            <button
              :class="{
                'discount-management__tab': true,
                'discount-management__tab--active': activeTab === tab.code,
              }"
              @click='activeTab = tab.code'
            >
              {{ tab.title }}
            </button>
          </li>
        </ul>
      </div>
      <div class='discount-management__content'>
        <div
          v-if="activeTab === 'discount'"
          class='discount-management__content-tab'
        >
          <discounts-editor :instructor-id='instructorId'/>
        </div>
        <div
          v-if="activeTab === 'promo'"
          class='discount-management__content-tab'
        >
          <promos-editor :instructor-id='instructorId'/>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import DiscountsEditor from '../DiscountsEditor/DiscountsEditor.vue'
import PromosEditor from '../PromosEditor/PromosEditor.vue'

export default {
  name: 'DiscountManagement',
  components: {
    DiscountsEditor,
    PromosEditor
  },
  data() {
    return {
      activeTab: 'discount',
      tabs: [
        {
          code: 'discount',
          title: 'Discount'
        },
        {
          code: 'promo',
          title: 'Promo Code'
        }
      ]
    }
  },
  props: {
    instructorId: {
      type: Number
    }
  }
}
</script>

<style
  lang='scss'
  scoped
>
.discount-management{
  margin-top: -10px!important;
}
@import './DiscountManagement.scss';
</style>
