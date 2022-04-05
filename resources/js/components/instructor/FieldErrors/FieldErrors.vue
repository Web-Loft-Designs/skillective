<template>
    <div class="field-errors">
        <span v-for="(error, index) in errors" :key="index" class="field-errors__text">{{ error }}</span>
    </div>
</template>

<script>
export default {
    name: "FieldErrors",
    model: {
        prop: "errors",
    },
    props: {
        errors: {
            type: Array,
            default: () => {
                return [];
            },
        },
    },
}
</script>

<style lang="scss" scoped>
@import "./FieldErrors.scss";
</style>