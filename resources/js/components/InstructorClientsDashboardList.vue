<template>
  <div class='clienst-outer-wrapper--dashboard clienst-outer-wrapper'>
    <div class='table-responsive'>
      <div v-if='errorText' class='has-error'>{{ errorText }}</div>
      <div v-if='successText' class='has-success'>{{ successText }}</div>

      <table class='table'>
        <thead>
        <tr>
          <th class='w-55'></th>
          <th class='w-100px'>Instagram</th>
          <th class='w-140'>Name</th>
          <th class='w-140'>Email</th>
          <th class='w-140'>Phone</th>
          <th>Skills</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for='(client, index) in listItems' :key='index'>
          <td><img :src='client.profile.image' alt=''/></td>
          <td class='width-fix'>
            <a
              class='width-fix-content'
              v-if='client.profile.instagram_handle != null'
              :href="
                  'https://www.instagram.com/' + client.profile.instagram_handle
                "
              target='_blank'
            >{{ '@' + client.profile.instagram_handle }}</a
            >
          </td>
          <td class='width-fix'>
            <div class='width-fix-content'>
              <a :href="'/profile/' + client.id" class='link-to-profile'>{{
                  client.full_name
                }}</a>
            </div>
          </td>
          <td class='width-fix'>
            <div class='width-fix-content'>{{ client.email }}</div>
          </td>
          <td>{{ client.profile.mobile_phone }}</td>
          <td v-html='getClientGenresList(client.genres)'></td>

          <td>
            <a
              class='btn-notify'
              @click='notifyClient(client)'
              v-if='checkClientProfile(client)'
            >
              Message
            </a
            >
            <a class='btn-delete' @click='deleteClient(client)'>Delete</a>
          </td>
        </tr>
        </tbody>
      </table>

      <div class='mobile--clients-dashboard-outer'>
        <div
          class='mobile--user-b-item'
          v-for='(client, index) in listItems'
          :key='index'
        >
          <div class='user-left'>
            <img :src='client.profile.image'/>
          </div>
          <div class='user-right'>
            <ul>
              <li>
                <span class='k'> Student </span>
                <span class='v'> {{ client.full_name }} </span>
              </li>
              <li>
                <span class='k'> Email </span>
                <span class='v'> {{ client.email }} </span>
              </li>
              <li>
                <span class='k'> Phone </span>
                <span class='v'>
                  {{ client.profile.mobile_phone }}
                </span>
              </li>
            </ul>
            <div class='user-b-bottom'>
              <a
                class='btn-notify'
                @click='notifyClient(client)'
                v-if='checkClientProfile(client)'
              >
                Message
              </a>
              <a class='btn-delete' @click='deleteClient(client)'>Delete</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <confirmation-popup ref='confirmationPopup'/>

  </div>
</template>

<script>
import siteAPI from '../mixins/siteAPI.js'
import skillectiveHelper from '../mixins/skillectiveHelper.js'
import ConfirmationPopup from './instructor/ConfirmationPopup/ConfirmationPopup.vue'

export default {
  mixins: [siteAPI, skillectiveHelper],
  components: {
    ConfirmationPopup
  },
  props: {
    clients: null
  },
  data() {
    return {}
  },
  methods: {
    checkClientProfile(client) {
      return client.profile?.notification_methods?.length > 0
    },
    confirmDelete(text, action) {
      this.$refs.confirmationPopup.showConfirm(text, () => {
        action()
      })
    },
    deleteClient(client) {
      this.confirmDelete('Are you sure you want to delete this client?', () => {
        this.apiDelete('/api/instructor/client/' + client.id)
      })
    },
    notifyClient(client) {
      this.$root.$emit('initNotificationsForm', [client])
    },
    getClients() {
      let queryParams = {}
      queryParams.limit = 5

      this.apiGet('/api/instructor/clients', {
        params: queryParams
      })
    },
    componentHandleGetResponse(responseData) {
      this.listItems = responseData.data.data
    },
    componentHandleDeleteResponse(responseData) {
      this.getClients()
    },
    getClientGenresList(clientGenres) {
      var _titles = []
      if (clientGenres.length > 3) {
        for (var i = 0; i < 3; i++) _titles.push(clientGenres[i].title)
        if (_titles.length > 0)
          return (
            '<span class="bars">' +
            _titles.join('</span><span class="bars">') +
            '</span>'
          )
        return ''
      } else {
        for (var i = 0; i < clientGenres.length; i++)
          _titles.push(clientGenres[i].title)
        if (_titles.length > 0)
          return (
            '<span class="bars">' +
            _titles.join('</span><span class="bars">') +
            '</span>'
          )
        return ''
      }
    }
  },
  created: function () {
    this.listItems = this.clients
  },
  mounted() {
    this.$root.$on('instructorClientsAdded', () => {
      this.getClients()
    })
  }
}
</script>
