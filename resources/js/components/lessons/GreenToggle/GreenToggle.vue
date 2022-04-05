<template>
  <div class="green-toggle">
    <span class="green-toggle__label">{{ label }}</span>
    <button @click.prevent="content = !content" :class="{
        'green-toggle__button': true,
        'green-toggle__button--active': content,
      }">
      <div class="green-toggle__content">
        <span
          class="green-toggle__enabled-text"
          v-if="value"
        ><span>{{ enabledText }}</span></span>
        <span
          class="green-toggle__disabled-text"
          v-else
        ><span>{{ disabledText }}</span></span>
      </div>
    </button>
  </div>
</template>

<script>
export default {
  name: "GreenToggle",
  props: {
    label: {
      type: String,
      default: "",
    },
    enabledText: {
      type: String,
      default: "Enabled",
    },
    disabledText: {
      type: String,
      default: "Disabled",
    },
    value: {
      type: Boolean,
      default: false,
    }
  },
  data() {
    return {
      content: this.value,
    }
  },
  watch: {
    content(newValue) {
      this.$emit("input", newValue);
    }
  },
}
</script>

<style lang="scss" scoped>
@import "./GreenToggle.scss";
</style>
