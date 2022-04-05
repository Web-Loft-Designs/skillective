<template>
    <div :class="{
        'select-with-search': true,
        'select-with-search--small': height == 'small',
    }">
        <vue-select 
            :options="options" 
            v-model="selectedValue" 
            :placeholder="placeholder" 
            :clearable="false"
        />
    </div>
</template>

<script>
import { VueSelect } from 'vue-select';
import 'vue-select/dist/vue-select.css';

export default {
    name: "SelectWithSearch",
    components: {
        VueSelect,
    },
    props: {
        options: {
            type: Array,
            default: () => {
                return [];
            },
        },
        placeholder: {
            type: String,
            default: "",
        },
        height: {
            type: String,
            default: null,
        },
    },
    data() {
        return {
            selectedValue: this.options[0],
        }
    },
    watch: {
        selectedValue(newValue) {
            this.$emit("value-changed", newValue);
        },
    },
    methods: {
        selectByValue(val) {
            this.options.map((option) => {
                if (option.value == val) {
                    this.selectedValue = option;
                }
            });
        },
    },
}
</script>

<style lang="scss" scoped>
@import "./SelectWithSearch.scss";
</style>