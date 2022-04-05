<template>
    <button :title="title" @click.prevent="click()" class="close-button">{{ title }}</button>
</template>

<script>
export default {
    name: "CloseButton",
    methods: {
        click() {
            this.$emit("click");
        },
    },
    props: {
        title: {
            type: String,
            default: "Close",
        },
    }
}
</script>

<style lang="scss" scoped>
@import "./CloseButton.scss";
</style>