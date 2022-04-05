<template>
  <button class="add-lesson-time-button" @click.prevent="openPopup()">Add lesson time</button>
</template>

<script>
export default {
  name: "AddLessonTimeButton",
  props: {
    noArrow: Boolean,
  },
  data() {
    return {
      opened: false,
    }
  },
  methods: {
    openPopup() {
      this.$root.$emit('openAddLessonModal');
    }
  },
}
</script>

<style lang="scss" scoped>
@import "./AddLessonTimeButton.scss";
</style>