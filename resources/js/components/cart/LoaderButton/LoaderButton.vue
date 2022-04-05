<template>
  <button class="loader-button" v-if="isLoading" disabled>
    <div class="anim-loader">
      <div class="anim-loader__bar"></div>
    </div>
  </button>
  <button 
    :class="{
      'loader-button': true,
      'loader-button--disabled': disabled,
    }"
    :disabled="disabled"
    @click.prevent="buttonClick()"
    v-else
  >
    {{ text }}
  </button>
</template>

<script>
export default {
  name: "LoaderButton",
  props: {
    isLoading: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    text: {
      type: String,
      default: "",
    },
  },
  methods: {
    buttonClick() {
      this.$emit('click')
    },
  },
}
</script>

<style lang="scss" scoped>
@import "./LoaderButton.scss";
</style>