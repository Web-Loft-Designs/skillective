<template>
  <div class='discount-number-input'>
    <input
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
  name: 'DiscNumberInput',
  props: {
    value: {
      type: [String, Number],
      default: null
    },
    disabled: {
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
        this.content = Number(val)
      } else {
        event.target.value = this.content
      }
    }
  },
  watch: {
    value(newValue) {
      this.content = newValue
    },
    content(newValue) {
      this.$emit('input', newValue)
    }
  }
}
</script>

<style
  lang='scss'
  scoped
>
@import './DiscNumberInput.scss';
</style>