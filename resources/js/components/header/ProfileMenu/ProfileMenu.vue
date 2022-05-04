<template>
    <nav-drop-down class="profile-menu" :links="links" :no-arrow="true">
        <template v-slot:button>
            <div class="profile-menu__flex">
              <span class="profile-menu__text">{{ text }}</span>
              <span v-if="subtext && subtext != ' '" class="profile-menu__subtext">{{ subtext }}</span>
            </div>
            <img :src="profileImageUrl" class="profile-menu__image" alt="Profile image" />
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
    img: {
      type: String,
      default: "",
    },
    text: {
      type: String,
      default: "",
    },
    subtext: {
      type: String,
      default: "",
    },
    links: {
      type: Array,
      default: () => {
        return [];
      },
    },
  },
  data() {
    return {
      profileImageUrl: this.img,
    };
  },
  async mounted() {
    this.$root.$on("updateProfileImage", ({ imageUrl }) => {
      this.profileImageUrl = imageUrl;
    });
  },
}
</script>

<style lang="scss" scoped>
@import "./ProfileMenu.scss";
</style>