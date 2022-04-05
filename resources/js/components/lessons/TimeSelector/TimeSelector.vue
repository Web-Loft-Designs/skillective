<template>
    <div class="time-selector">
        <vue-timepicker
            :minute-interval="15"
            v-model="content"
            format="h:mm a"
            hour-label="Hour"
            minute-label="Minute"
            apm-label="AM/PM"
        ></vue-timepicker>
    </div>
</template>

<script>
import VueTimepicker from "vue2-timepicker/src/vue-timepicker.vue";

export default {
    name: "TimeSelector",
    props: {
        value: {
            type: Object,
            default: () => {
                return null;
            },
        },
    },
    components: {
        VueTimepicker,
    },
    data() {
        return {
            content: this.value,
        }
    },
    watch: {
        content(newValue) {
            this.$emit("input", newValue);
        }
    },
};
</script>

<style lang="scss" scoped>
@import "./TimeSelector.scss";
</style>
