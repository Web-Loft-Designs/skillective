<template>
    <div class="confirmation-popup">
        <transition name="show">
            <div v-if="opened" class="confirmation-popup__backdrop" @click.prevent="opened = false"></div>
        </transition>

        <transition name="scalein">
            <div v-if="opened" class="confirmation-popup__popup-container">
                <div class="confirmation-popup__popup" v-scroll-lock="opened" @click.stop>
                    <close-button class="confirmation-popup__close" @click="opened = false" />
                    <div class="confirmation-popup__content">
                        <h3 class="confirmation-popup__heading">{{ heading }}</h3>
                        <button
                            class="confirmation-popup__button"
                            @click.prevent="yes()"
                            ref="yesButton"
                        >Yes</button>
                    </div>
                </div>
            </div>
        </transition>

    </div>
</template>

<script>
import CloseButton from "../../student/CloseButton/CloseButton.vue";

export default {
    name: "ConfirmationPopup",
    components: {
        CloseButton,
    },
    data() {
        return {
            opened: false,
            heading: "",
            action: null,
        }
    },
    methods: {
        showConfirm(heading, action) {
            this.opened = true;
            this.heading = heading;
            this.action = action;
            setTimeout(() => {
                this.$refs.yesButton.focus();
            }, 0);
        },
        yes() {
            this.action();
            this.opened = false;
        },
    },
}
</script>

<style lang="scss" scoped>
@import "./ConfirmationPopup.scss";
</style>