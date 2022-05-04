<template>
    <div class="info-popup">
        <transition name="show">
            <div v-if="opened" class="info-popup__backdrop" @click.prevent="opened = false"></div>
        </transition>

        <transition name="scalein">
            <div v-if="opened" class="info-popup__popup-container">
                <div class="info-popup__popup" v-scroll-lock="opened" @click.stop>
                    <div class="info-popup__content">
                        <h3 class="info-popup__heading">{{ heading }}</h3>
                        <button
                            class="info-popup__button"
                            @click.prevent="opened = false"
                            ref="yesButton"
                        >OK</button>
                    </div>
                </div>
            </div>
        </transition>

    </div>
</template>

<script>
export default {
    name: "InfoPopup",
    data() {
        return {
            opened: false,
            heading: "",
        }
    },
    methods: {
        showInfo(heading) {
            this.opened = true;
            this.heading = heading;
            setTimeout(() => {
                this.$refs.yesButton.focus();
            }, 0);
        },
    },
}
</script>

<style lang="scss" scoped>
@import "./InfoPopup.scss";
</style>