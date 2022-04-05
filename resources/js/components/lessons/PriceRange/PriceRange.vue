<template>
    <div class="price-range">
      <vue-slider
        @change="changePrice(priceConvert[0], priceConvert[1])"
        :tooltip="'none'"
        :min="0"
        :max="1000"
        :min-range="1"
        :enable-cross="false"
        v-model="priceConvert"
        :height="2"
        :dotSize="16"
        :duration="0.25"
      ></vue-slider>
    </div>
</template>

<script>
export default {
    name: "TimeRange",
    props: {
        value: {
            type: Object,
            default: () => {
                return null;
            },
        },
    },
    beforeMount () {
        this.priceConvert = [ this.value.from, this.value.to ];
    },
    data() {
        return {
            priceRange: this.value,
            priceConvert: [ 10, 100 ],
        }
    },
    watch: {
        priceRange(newValue) {
            this.$emit("input", newValue);
        },
        priceConvert() {
            this.emitValue();
        },
    },
    methods: {
        emitValue() {
            this.priceRange = {
                from: this.priceConvert[0],
                to: this.priceConvert[1],
                text: `$${this.priceConvert[0]} - $${this.priceConvert[1]}`,
            };
        },
        changePrice(from, to) {
          this.priceConvert = [ from, to ];
        },
    },
};
</script>

<style lang="scss" scoped>
@import "../TimeRange/TimeRange.scss";
</style>
