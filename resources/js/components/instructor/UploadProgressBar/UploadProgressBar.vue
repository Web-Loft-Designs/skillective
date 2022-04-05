<template>
  <div class="upload-progress-bar" v-if="progress > 0 && progress < 101">
    <div class="upload-progress-bar__container">
      <span 
        class="upload-progress-bar__line"
        :style="lineWidthStyle"
      ></span>
    </div>
    <span class="upload-progress-bar__percent">
      <span v-if="progress >= 100">Processing...</span>
      <span v-else>{{ percent }}%</span>
    </span>
  </div>
</template>

<script>
export default {
  name: "UploadProgressBar",
  model: {
    prop: "progress",
  },
  props: {
    progress: {
      type: Number,
      default: 0,
    },
  },
  computed: {
    lineWidthStyle() {
      return `width: ${ this.progress }%`;
    },
    percent() {
      return (Math.round(this.progress * 100) / 100).toFixed(2);
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./UploadProgressBar.scss";
</style>