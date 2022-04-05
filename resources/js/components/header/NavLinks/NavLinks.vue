<template>
  <div class="nav-links">
    <button class="nav-links__button" @click.prevent="opened = !opened">Menu</button>
    <ul ref="navLinksList" :class="{
      'nav-links__list': true,
      'nav-links__list--show': opened,
    }">
      <slot></slot>
    </ul>
  </div>
</template>

<script>
export default {
  name: "NavLinks",
  data() {
    return {
      opened: false,
    }
  },
}
</script>

<style lang="scss" scoped>
@import "./NavLinks.scss";
</style>