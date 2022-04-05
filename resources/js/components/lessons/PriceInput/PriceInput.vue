<template>
    <div class="price-input">
        <input 
            type="text"
            @input="change"
            @change="change"
            :value="content"
        />
    </div>
</template>

<script>
export default {
    name: "PriceInput",
    props: {
        value: {
            type: Number,
            default: null,
        },
    },
    data() {
        return {
            content: this.value,
        }
    },
    methods: {
        change(event) {
            let val = event.target.value.trim();
            if (/^[1-9]\d*$|^$/.test(val)) {
                this.content = Number(val);
            } else {
                event.target.value = this.content;
            }
        },
    },
    watch: {
        content(newValue) {
            this.$emit("input", newValue);
        }
    },
};
</script>

<style lang="scss" scoped>
@import "./PriceInput.scss";
</style>
