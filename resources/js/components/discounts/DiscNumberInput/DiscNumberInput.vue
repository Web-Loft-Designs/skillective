<template>
    <div class="discount-number-input">
        <input 
            type="text"
            @input="change"
            @change="change"
            :value="content"
            :disabled="disabled"
        />
    </div>
</template>

<script>
export default {
    name: "DiscNumberInput",
    props: {
        value: {
            type: Number,
            default: null,
        },
        disabled: {
            type: Boolean,
            default: false,
        }
    },
    data() {
        return {
            content: this.value,
        }
    },
    methods: {
        change(event) {
            const val = event.target.value.trim();
            if (/^[1-9]\d*$|^$/.test(val)) {
                let num = Number(val);
                this.content = num;
            } else {
                event.target.value = this.content;
            }
        },
    },
    watch: {
        value(newValue) {
            this.content = newValue;
        },
        content(newValue, oldValue) {
            this.$emit("input", newValue);
        }
    },
};
</script>

<style lang="scss" scoped>
@import "./DiscNumberInput.scss";
</style>