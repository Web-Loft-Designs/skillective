<template>
    <div class="pagination">
        <paginate 
            v-if="pageCount > 1"
            v-model="page"
            :page-count="pageCount"
            :page-range="3"
            :margin-pages="1"
            :click-handler="clickCallback"
            :prev-text="'Prev'"
            :next-text="'Next'"
            :container-class="'pagination'"
            :page-class="'page-item'"
        />
    </div>
</template>

<script>
import Paginate from 'vuejs-paginate';

export default {
    name: "Pagination",
    components: {
        Paginate,
    },
    props: {
        pageCount: {
            type: Number,
            default: 1,
        },
        currentPage: {
            type: Number,
            default: 1,
        },
    },
    methods: {
        clickCallback(pageNum) {
            this.$emit("page-changed", pageNum);
        }
    },
    data() {
        return {
            page: this.currentPage,
        }
    },
    watch: {
        currentPage(newValue) {
            this.page = newValue;
        },
    },
}
</script>

<style lang="scss" scoped>
@import "./Pagination.scss";
</style>