<template>
    <ul class="additional-lessons-filters">
        <li>
            <filter-popup 
                text="Time range" 
                @open="closeFilters([ 'priceFilter' ])" 
                ref="timeFilter"
                @clear="clearTime()"
                @save="saveTime()"
            >
                <div class="additional-lessons-filters__row">
                    <div class="additional-lessons-filters__col">
                        <span class="additional-lessons-filters__heading">{{ timeRange.text }}</span>
                        <time-range v-model="timeRange" />
                    </div>
                </div>
            </filter-popup>
        </li>
        <li>
            <filter-popup 
                text="Price range" 
                @open="closeFilters([ 'timeFilter' ])" 
                ref="priceFilter"
                @clear="clearPrice()"
                @save="savePrice()"
            >
                <div class="additional-lessons-filters__row">
                    <div class="additional-lessons-filters__col">
                        <span class="additional-lessons-filters__heading">{{ priceRange.text }}</span>
                        <price-range v-model="priceRange" />
                    </div>
                </div>
            </filter-popup>
        </li>
    </ul>
</template>

<script>
import FilterPopup from "../FilterPopup/FilterPopup.vue";
import TimeSelector from "../TimeSelector/TimeSelector.vue";
import TimeRange from "../TimeRange/TimeRange.vue";
import PriceRange from "../PriceRange/PriceRange.vue";
import urlHelper from "../../../helpers/urlHelper";

export default {
    name: "AdditionalLessonsFilters",
    components: {
        FilterPopup,
        TimeSelector,
        TimeRange,
        PriceRange,
    },
    data() {
        return {
            timeRange: {
                from: "6:00",
                to: "12:00",
                text: "6:00 AM - 12:00 PM",
            },
            timeSaved: false,
            priceRange: {
                from: 10,
                to: 100,
                text: "$10 -$100",
            },
            priceSaved: false,
        }
    },
    created() {
        this.parseSearchUrl();
    },
    methods: {
        parseSearchUrl() {
            const params = urlHelper.parseQueryParams();

            if (params.time_from) {
                this.timeRange.from = params.time_from;
            }
            if (params.time_to) {
                this.timeRange.to = params.time_to;
            }
            if (params.price_from) {
                this.priceRange.from = Number(params.price_from);
            }
            if (params.price_to) {
                this.priceRange.to = Number(params.price_to);
                if (!params.price_from) {
                    this.priceRange.from = 0;
                }
            }

            if (params.time_from && params.time_to) {
                this.timeSaved = true;
            }

            if (params.price_from || params.price_to) {
                this.priceSaved = true;
            }
        },
        buildSearchUrl() {
            const params = {
                time_from: null,
                time_to: null,
                price_from: null,
                price_to: null,
            };
            if (this.timeSaved) {
                params.time_from = this.timeRange.from;
                params.time_to = this.timeRange.to;
            }
            if (this.priceSaved) {
                params.price_from = this.priceRange.from;
                params.price_to = this.priceRange.to;
            }
            this.$root.$emit("lessonsLoadLessons", params);
        },
        closeFilters(filters) {
            filters.map((str) => {
                this.$refs[str].close();
            });
        },
        clearTime() {
            this.timeRange.from = "6:00";
            this.timeRange.to = "12:00";
            this.timeSaved = false;
            this.buildSearchUrl();
        },
        clearPrice() {
            this.priceRange.from = 10;
            this.priceRange.to = 100;
            this.priceSaved = false;
            this.buildSearchUrl();
        },
        saveTime() {
            this.timeSaved = true;
            this.buildSearchUrl();
        },
        savePrice() {
            this.priceSaved = true;
            this.buildSearchUrl();
        },
    },
};
</script>

<style lang="scss" scoped>
@import "./AdditionalLessonsFilters.scss";
</style>
