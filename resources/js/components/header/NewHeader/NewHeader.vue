<template>
  <header :class="{
    'new-header': true,
    'new-header--no-shadow': noShadow,
  }">
    <a href='/' class='new-header__logo'>
      <img :src="logoSrc || 'images/logo.png'" />
      <!--      <span>** Stay tuned for full platform & App launch fall 2021</span>-->
    </a>
    <div class='new-header__slot-center'>
      <slot name='center'></slot>
    </div>
    <div class='new-header__slot-right'>
      <slot name='right'></slot>
    </div>
  </header>
</template>

<script>
export default {
  name: 'NewHeader',
  data() {
    return {
      noShadow: false,
    }
  },
  props: {
    logoSrc: String,
  },
  methods: {
    setNoShadow(b) {
      this.noShadow = b
    },
  },
}
</script>

<style lang='scss' scoped>
@import "./NewHeader.scss";
</style>