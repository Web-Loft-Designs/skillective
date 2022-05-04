<template>
    <div :class="{
        'select-with-search': true,
        'select-with-search--small': type == 'small',
        'select-with-search--form': type == 'form',
    }">
        <vue-select 
            :options="options" 
            v-model="selectedValue" 
            :placeholder="placeholder" 
            :clearable="false"
            :disabled="disabled"
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
        value: {
            default: null,
        },
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
        type: {
            type: String,
            default: null,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            selectedValue: this.options[0],
        }
    },
    mounted() {
        this.checkValueChanged();
    },
    watch: {
        value() {
            this.checkValueChanged();
        },
        selectedValue(newValue) {
            
            this.$emit("value-changed", newValue);
        },
    },
    methods: {
        checkValueChanged() {
            
            if (this.value) {
                this.options.forEach((op) => {
                    if ((op.value && op.value == this.value) || (op == this.value)) {
                        // this.selectedValue = op;
                        console.log(this.selectedValue)
                    }
                });
            }
        },
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