<template>
    <div class="cart-icon">
        <button class="cart-icon__button" @click.prevent="showMiniCart()">
            <svg
                width="22"
                height="22"
                viewBox="0 0 22 22"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M5.5 1.83337L2.75 5.50004V18.3334C2.75 18.8196 2.94315 19.2859 3.28697 19.6297C3.63079 19.9736 4.0971 20.1667 4.58333 20.1667H17.4167C17.9029 20.1667 18.3692 19.9736 18.713 19.6297C19.0568 19.2859 19.25 18.8196 19.25 18.3334V5.50004L16.5 1.83337H5.5Z"
                    stroke="#0AAB14"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
                <path
                    d="M2.75 5.5H19.25"
                    stroke="#0AAB14"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
                <path
                    d="M14.6667 9.16663C14.6667 10.1391 14.2804 11.0717 13.5928 11.7594C12.9051 12.447 11.9725 12.8333 11 12.8333C10.0276 12.8333 9.09495 12.447 8.40732 11.7594C7.71968 11.0717 7.33337 10.1391 7.33337 9.16663"
                    stroke="#0AAB14"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
            <div class="cart-icon__indicator" v-if="computedIsDotNeeded"></div>
        </button>
        <mini-cart />
    </div>
</template>

<script>
import MiniCart from "../MiniCart/MiniCart.vue";
import { mapActions, mapMutations, mapGetters } from "vuex";

export default {
    name: "CartIcon",
    components: {
        MiniCart
    },
    props: {
        guestMode: {
            type: Boolean,
            default: false
        }
    },
    async mounted() {
        await this.setGuestMode(Boolean(this.guestMode));
        await this.fetchDotNeeded();
    },
    computed: {
        computedIsDotNeeded() {
            return this.isDotNeeded();
        }
    },
    methods: {
        showMiniCart() {
            this.$root.$emit("showMiniCart");
        },
        ...mapActions({
            fetchDotNeeded: "fetchDotNeeded"
        }),
        ...mapMutations({
            setGuestMode: "setGuestMode"
        }),
        ...mapGetters({
            isDotNeeded: "isDotNeeded"
        })
    }
};
</script>

<style lang="scss" scoped>
@import "./CartIcon.scss";
</style>
