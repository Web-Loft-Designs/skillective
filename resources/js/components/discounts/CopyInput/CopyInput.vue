<template>
  <div :class="{
      'copy-input': true,
      'copy-input--show-effect': showEffect,
    }">
    <input
      type="text"
      ref="copyInput"
      :value="value"
      :readonly="readonly"
    />
    <button title="Copy" @click.prevent="copyValue()">
      <img alt="Copy" src="/images/copy-icon.svg" />
      Copy
      <span>Copied</span>
    </button>
  </div>
</template>

<script>
export default {
  name: "CopyInput",
  props: {
    readonly: {
      type: Boolean,
      default: false,
    },
    value: {
      type: String,
      default: "",
    },
  },
  data() {
    return {
      showEffect: false,
      timeout: null,
    }
  },
  methods: {
    copyValue() {
      if (!this.timeout) {
        navigator.clipboard.writeText(this.$refs.copyInput.value);
        this.showEffect = true;
        this.timeout = setTimeout(() => {
          this.showEffect = false;
          this.timeout = null;
        }, 3000);
      }
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./CopyInput.scss";
</style>