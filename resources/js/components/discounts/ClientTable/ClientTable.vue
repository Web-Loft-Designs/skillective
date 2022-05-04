<template>
  <div class="client-table">

    <anim-loader v-if="isLoading" />

    <div v-else class="client-table__scroll">
      <div class="client-table__head">
        <div
          v-if="checkable"
          class="client-table__col client-table__col--checkbox"
        >
          <input v-model="checkAll" @click="toggleCheckAll()" type="checkbox" />
        </div>
        <div
          v-for="(col, colIndex) in cols"
          :key="colIndex"
          :class="{
            'client-table__col': true,
            'client-table__col--image': col.type == 'image',
            'client-table__col--long': col.type == 'long',
          }"
        >
          <span>{{ col.title }}</span>
        </div>
      </div>
      <div class="client-table__body">
        <div
          v-for="(row, rowIndex) in clients"
          :key="rowIndex"
          class="client-table__row"
        >
          <div
            v-if="checkable"
            class="client-table__col client-table__col--checkbox"
          >
            <input :checked="computedCheckedRows[rowIndex]" @click="toggleCheckRow(rowIndex)" type="checkbox" />
          </div>

          <div
            :class="{
              'client-table__col': true,
              'client-table__col--image': true,
            }"
          >
            <img :src="row.profile.image" />
          </div>

          <div
            :class="{
              'client-table__col': true,
            }"
          >
            <span> {{ row.full_name }}</span>
          </div>
          <div
            :class="{
              'client-table__col': true,
              'client-table__col--long': true,
            }"
          >
            <span> {{ row.email }}</span>
          </div>
          <div
            :class="{
              'client-table__col': true,
            }"
          >
            <span> {{ row.profile.mobile_phone }}</span>
          </div>
        </div>
      </div>
    </div>

    <div v-if="!isLoading" class="client-table__foot">
      <div class="client-table__foot-left-side">
        <span>Show</span>
        <select-with-search
          :options="pageCounts"
          @value-changed="perPageChanged($event)"
          type="small"
          ref="sortSelect"
        />
      </div>
      <div class="client-table__foot-right-side">
        <span>
          {{ computedFirstClient }}-{{ computedLastClient }} of {{ pagination.totalClients }}
        </span>
        <button
          class="client-table__pagi-prev"
          @click="prevPage()"
          :disabled="pagination.currentPage <= 1"
        >Prev page</button>
        <button
          class="client-table__pagi-next"
          @click="nextPage()"
          :disabled="pagination.currentPage >= pagination.pageCount"
        >Next page</button>
      </div>
    </div>

  </div>
</template>

<script>
import AnimLoader from "../../cart/AnimLoader/AnimLoader.vue";
import SelectWithSearch from "../../student/SelectWithSearch/SelectWithSearch.vue";
import discountPromoService from "../../../services/discountPromoService";

export default {
  name: "ClientTable",
  components: {
    SelectWithSearch,
    AnimLoader,
  },
  props: {
    checkable: {
      type: Boolean,
      default: true,
    },
  },
  data() {
    return {
      isLoading: false,
      checkAll: false,
      checkedClients: [],
      cols: [
        {
          title: "Photo",
          type: "image",
        },
        {
          title: "Student",
          type: "text",
        },
        {
          title: "Email",
          type: "long",
        },
        {
          title: "Phone",
          type: "text",
        },
      ],
      pageCounts: [ 25, 50 ],
      clients: [],
      pagination: {
        currentPage: 1,
        pageCount: 1,
        totalClients: 0,
        clientsPerPage: 25,
        clientsCount: 0,
      },
    };
  },
  async created() {
    await this.loadClients();
  },
  computed: {
    computedFirstClient() {
      return (this.pagination.currentPage * this.pagination.clientsPerPage) - this.pagination.clientsPerPage + 1;
    },
    computedLastClient() {
      return this.computedFirstClient + this.pagination.clientsCount - 1;
    },
    computedCheckedRows() {
      return this.clients.map((v) => {
        return this.checkedClients.includes(v.id);
      });
    },
  },
  watch: {
    computedCheckedRows(newValue) {
      newValue.forEach((v) => {
        if (!v) {
          this.checkAll = false;
        }
      });
    },
    checkedClients(newValue) {
      this.$emit("checked-clients-changed", newValue);
    },
  },
  methods: {
    toggleCheckAll() {
      this.checkAll = !this.checkAll;
      if (this.checkAll) {
        this.clients.map((v, i) => {
          const ind = this.checkedClients.indexOf(v.id);
          if (ind == -1) {
            this.checkedClients.push(v.id);
          }
        });
      } else {
        this.clients.map((v, i) => {
          const ind = this.checkedClients.indexOf(v.id);
          if (ind != -1) {
           this.checkedClients.splice(ind, 1);
          }
        });
      }
    },
    toggleCheckRow(rowIndex) {
      const ind = this.checkedClients.indexOf(this.clients[rowIndex].id)
      if (ind == -1) {
        this.checkedClients.push(this.clients[rowIndex].id);
      } else {
        this.checkedClients.splice(ind, 1);
      }
    },
    async loadClients() {
      this.isLoading = true;
      const data = await discountPromoService.getInstructorClients({
        page: this.pagination.currentPage,
        per_page: this.pagination.clientsPerPage,
      });
      this.clients = data.clients;
      this.pagination = data.pagination;
      this.isLoading = false;
    },
    nextPage() {
      if (this.pagination.currentPage < this.pagination.pageCount) {
        this.pagination.currentPage++;
      }
      this.loadClients();
    },
    prevPage() {
      if (this.pagination.currentPage > 1) {
        this.pagination.currentPage--;
      }
      this.loadClients();
    },
    perPageChanged(value) {
      this.pagination.clientsPerPage = value;
      this.loadClients();
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./ClientTable.scss";
</style>