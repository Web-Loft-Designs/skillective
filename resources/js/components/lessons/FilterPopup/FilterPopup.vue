<template>
  <div class="filter-popup">
    <button @click.prevent="active = !active" :class="{
      'filter-popup__button': true,
      'filter-popup__button--active': active,
    }">{{ text }}</button>
    <transition name="show">
      <div class="filter-popup__popup" v-if="active">
        <div class="filter-popup__body">
          <slot />
        </div>
        <div class="filter-popup__actions">
          <button class="filter-popup__clear-btn" @click.prevent="clear()">Clear</button>
          <button class="filter-popup__save-btn" @click.prevent="save()">Save</button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
    name: "FilterPopup",
    props: {
      text: {
        type: String,
        default: "",
      },
    },
    data() {
      return {
        active: false,
      }
    },
    methods: {
      close() {
        this.active = false;
      },
      clear() {
        this.close();
        this.$emit("clear");
      },
      save() {
        this.close();
        this.$emit("save");
      },
    },
    watch: {
      active(newValue) {
        if (newValue) {
          this.$emit("open");
        }
      },
    },
};
</script>

<style lang="scss" scoped>
@import "./FilterPopup.scss";
</style>
