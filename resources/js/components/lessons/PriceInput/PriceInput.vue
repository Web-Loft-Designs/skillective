<template>
  <div
    :class="{
      'price-input': true,
      'price-input--percent-mode': percentMode,
    }"
  >
    <input
      ref='priceInput'
      :disabled='disabled'
      :value='content'
      type='text'
      @change='change'
      @input='change'
    />
  </div>
</template>

<script>
export default {
  name: 'PriceInput',
  props: {
    value: {
      type: [String, Number],
      default: null
    },
    disabled: {
      type: Boolean,
      default: false
    },
    percentMode: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      content: this.value
    }
  },
  methods: {
    change(event) {
      const val = event.target.value.trim()
      if (/^[1-9]\d*$|^$/.test(val)) {
        let num = Number(val)
        if (this.percentMode && num > 100) {
          num = 100
          event.target.value = 100
        }
        this.content = num
      } else {
        event.target.value = this.content
      }
    }
  },
  watch: {
    value(newValue) {
      this.content = newValue
    },
    content(newValue, oldValue) {
      this.$emit('input', newValue)
    },
    percentMode(newValue) {
      if (newValue) {
        if (this.content > 100) {
          this.content = 100
        }
      }
    }
  }
}
</script>

<style
  lang='scss'
  scoped
>
@import './PriceInput.scss';
</style>
