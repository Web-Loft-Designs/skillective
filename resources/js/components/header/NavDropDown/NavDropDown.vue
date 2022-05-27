<template>
  <li class="nav-drop-down">
    <button @click.prevent="opened = !opened" :class="{
      'nav-drop-down__button': true,
      'nav-drop-down__button--opened': opened,
      'nav-drop-down__button--no-arrow': noArrow,
    }">
      <slot name="button"></slot>
    </button>
    <transition name="scale">
      <ul class="nav-drop-down__list" v-if="opened">
        <slot></slot>
      </ul>
    </transition>
  </li>
</template>

<script>
export default {
  name: "NavDropDown",
  props: {
    noArrow: Boolean,
  },
  data() {
    return {
      opened: false,
    }
  },
  created() {
    document.addEventListener('click', (e) => {
      const dropBtn = document.querySelector('.nav-drop-down__button')
      const click = e.composedPath().includes(dropBtn)
      if (this.opened && !click) {
        this.opened = !this.opened
      }
    });
    this.$on('hook:beforeDestroy', () => document.removeEventListener('click', onClickOutside));
  },
}
</script>

<style lang="scss" scoped>
@import "./NavDropDown.scss";
</style>