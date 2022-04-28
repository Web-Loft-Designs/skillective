<template>
  <div class="content-viewer" v-html="content"></div>
</template>

<script>
export default {
  name: "ContentViewer",
  props: {
    content: {
      type: String,
      default: "",
    },
  },
};
</script>
