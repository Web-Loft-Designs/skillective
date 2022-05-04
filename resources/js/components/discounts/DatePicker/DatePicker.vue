<template>
    <div
        :class="{
            'date-picker': true,
            'date-picker--disabled': disabled
        }"
    >
        <dropdown-datepicker
            display-format="mdy"
            v-model="content"
            submit-format="yyyy-mm-dd"
            ref="datepicker"
            :disabled="disabled"
        ></dropdown-datepicker>
    </div>
</template>

<script>
import DropdownDatepicker from "vue-dropdown-datepicker";

export default {
    name: "DatePicker",
    components: {
        DropdownDatepicker
    },
    props: {
        value: {
            type: String,
            default: ""
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            content: this.value ? moment(this.value).format("YYYY-MM-DD") : ""
        };
    },
    mounted() {
        if (this.$refs.datepicker && this.value) {
            this.$refs.datepicker.day = Number(moment(this.value).format("DD"));
            this.$refs.datepicker.month = Number(
                moment(this.value).format("MM")
            );
            this.$refs.datepicker.year = Number(
                moment(this.value).format("YYYY")
            );
        }
        this.$emit("input", this.content);
    },
    watch: {
        value(newValue) {
            if (this.$refs.datepicker) {
                if (newValue) {
                    this.content = moment(newValue).format("YYYY-MM-DD");
                    this.$refs.datepicker.day = Number(
                        moment(newValue).format("DD")
                    );
                    this.$refs.datepicker.month = Number(
                        moment(newValue).format("MM")
                    );
                    this.$refs.datepicker.year = Number(
                        moment(newValue).format("YYYY")
                    );
                } else {
                    this.content = "";
                    this.$refs.datepicker.day = null;
                    this.$refs.datepicker.month = null;
                    this.$refs.datepicker.year = null;
                }
            }
        },
        content(newValue) {
            this.$emit("input", newValue);
        }
    }
};
</script>

<style lang="scss" scoped>
@import "./DatePicker.scss";
</style>
