<template>
  <div
    :class="{
      'select-with-search': true,
      'select-with-search--small': type === 'small',
      'select-with-search--form': type === 'form',
    }"
  >
    <vue-select
      v-model='selectedValue'
      :clearable='false'
      :disabled='disabled'
      :options='options'
      :placeholder='placeholder'
      @input="$emit('value-changed', selectedValue)"
    />
  </div>
</template>

<script>
import { VueSelect } from 'vue-select'
import 'vue-select/dist/vue-select.css'

export default {
  name: 'SelectWithSearch',
  components: {
    VueSelect
  },
  props: {
    value: {
      default: null
    },
    options: {
      type: Array,
      default: () => []
    },
    placeholder: {
      type: String,
      default: ''
    },
    type: {
      type: String,
      default: null
    },
    disabled: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      selectedValue: this.value
    }
  }
}
</script>

<style
  lang='scss'
  scoped
>
@import './SelectWithSearch.scss';
</style>