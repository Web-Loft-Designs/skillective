<template>
  <div class="tabs">
    <ul>
      <li
        @click="selectTab('bookings')"
        :class="{ 'is-active': active === 'bookings' }"
      >
        Bookings
      </li>
      <li
        @click="selectTab('clients')"
        :class="{ 'is-active': active === 'clients' }"
      >
        Clients
      </li>
    </ul>

    <div class="tab" v-if="active === 'bookings'">
      <instructor-bookings-dashboard-list
        :bookings="bookings"
        :bookings-meta="bookingsMeta"
      >
      </instructor-bookings-dashboard-list>
    </div>

    <div class="tab" v-if="active === 'clients'">
      <div class="clients-table">
        <div class="clients-table-header">

          <instructor-clients-add-form></instructor-clients-add-form>
        </div>

        <instructor-clients-dashboard-list :clients="clients">
        </instructor-clients-dashboard-list>

        <a href="/instructor/clients" class="btn btn-block btn-secondary"
          >View all</a
        >
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    bookings: null,
    bookingsMeta: {},
    clients: null,
  },
  data() {
    return { active: "bookings" };
  },
  mounted: function(){
    console.log(this.bookings)
  },
  methods: {
    selectTab(selectedTab) {
      this.active = selectedTab;
    },
  },
};
</script>



