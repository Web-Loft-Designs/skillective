<template>
    <nav-drop-down class="profile-menu" :links="links" :no-arrow="true">
        <template v-slot:button>
            <div class="profile-menu__flex">
              <span class="profile-menu__text">{{ text }}</span>
              <span v-if="subtext && subtext != ' '" class="profile-menu__subtext">{{ subtext }}</span>
            </div>
            <img :src="img" class="profile-menu__image" />
        </template>
        <template v-slot:default>
            <slot></slot>
        </template>
    </nav-drop-down>
</template>

<script>
import NavDropDown from "../NavDropDown/NavDropDown.vue";

export default {
  name: "ProfileMenu",
  components: {
    NavDropDown,
  },
  props: {
    img: String,
    text: String,
    subtext: String,
    links: Array,
  },
}
</script>

<style lang="scss" scoped>
@import "./ProfileMenu.scss";
</style>