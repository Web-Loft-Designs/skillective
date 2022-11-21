<template>
  <div>
    <div class='users-table-header'>
      <div class='table-top d-flex align-items-center'>
        <h2>
          Instructors
          <span
            v-if='invitedByInstagramHandle != null'
          >invited by {{ invitedByInstagramHandle }}</span
          >
        </h2>
        <div class='ml-3 d-flex align-items-center'>
          <button
            :class="{ active: showOnly == 'active' }"
            @click.prevent="toggleShowOnly('active')"
          >
            Active
          </button>
          <button
            :class="{ active: showOnly == 'approved' }"
            @click.prevent="toggleShowOnly('approved')"
          >
            Approved
          </button>
          <button
            :class="{ active: showOnly == 'suspended' }"
            @click.prevent="toggleShowOnly('suspended')"
          >
            Suspended
          </button>
          <button
            :class="{ active: showOnly == 'on_review' }"
            @click.prevent="toggleShowOnly('on_review')"
          >
            On Review
          </button>
          <button
            :class="{ active: showOnly == 'invited' }"
            @click.prevent="toggleShowOnly('invited')"
          >
            Invited
          </button>
        </div>
      </div>
      <div
        class='
          d-flex
          mt-5
          responsive-mobile
          justify-content-between
          align-items-center
        '
      >
        <div class='d-flex responsive-mobile align-items-center'>
          <div
            v-if="selectedItems.length > 0 && showOnly !== 'invited' "
            class='d-flex align-items-center'
          >
            <span
              class='btn btn-notify mr-2' @click='notifyManyUsers'
            >Contact {{ selectedItems.length }} instructor<span
              v-if='selectedItems.length > 1'
            >s</span
            ></span
            >
            <span
              class='btn btn-danger mr-2' @click='blockManyUsers'
            >Suspend {{ selectedItems.length }} instructor<span
              v-if='selectedItems.length > 1'
            >s</span
            ></span
            >
          </div>
          <input
            v-model='searchString'
            placeholder='Search instructor'
            type='text'
          />
        </div>
        <div v-if='!invitedByInstagramHandle' class='invite-buttons'>
          <button
            v-if='isResendInvite'
            :disabled='!selectedItems.length'
            class='btn-green'
            @click='resendInvite'
          >
            Resend Invite Instructors
          </button>
          <modal-invate
            v-else
            :invite-type="'instructors'"
            :text-button="'Invite Instructors'"
            :text-title="'Invite instructors'"
          ></modal-invate>
        </div>
      </div>

      <form
        ref='loginAsForm'
        action='/backend/login-as'
        method='post'
        style='display: none'
      >
        <input :value='csrf' name='_token' type='hidden'/>
        <input :value='loginAsUserId' name='user_id' type='hidden'/>
      </form>
    </div>
    <div class='table-responsives'>
      <div v-if='errorText' class='has-error'>{{ errorText }}</div>
      <div v-if='successText' class='has-success'>{{ successText }}</div>
      <div v-if='invitedMessage.success' class='has-success mt-2'>{{ invitedMessage.success }}</div>
      <div v-if='invitedMessage.error' class='has-error mt-2'>{{ invitedMessage.error }}</div>

      <table
        v-if='isResendInvite'
        class='table table-invited'
      >
        <thead>
        <tr>
          <th class='cb-td-with-start' scope='col'>
              <span class='checkbox-wrapper'>
                <label for='checkAll'>
                  <input
                    id='checkAll'
                    v-model='allSelected'
                    :indeterminate.prop='indeterminate'
                    type='checkbox'
                    @change='selectAll'
                  />
                  <span
                    :class='{ indeterminate: indeterminate === true }'
                    class='checkmark'
                  ></span>
                </label>
              </span>
          </th>
          <th class='email-header'>Email</th>
        </tr>
        </thead>
        <tbody>
        <tr v-for='(user, index) in listItems' :key='index'>
          <td class='cb-td-with-start'>
              <span class='checkbox-wrapper cb--with-start'>
                <label>
                  <input
                    v-model='selectedItems'
                    :value='user.email'
                    type='checkbox'
                    @change='select'
                  />
                  <span class='checkmark'></span>
                </label>
              </span>
          </td>
          <td>
            <div class='d-flex align-items-center'>
              <img :src="require('../../../images/default_profile_image.png').default" class='mr-4'>
              <div> {{ user.email }}</div>
            </div>
          </td>
        </tr>
        </tbody>
      </table>

      <table
        v-else
        class='table'
      >
        <thead>
        <tr>
          <th class='cb-td-with-start' scope='col'>
              <span class='checkbox-wrapper'>
                <label for='checkAll'>
                  <input
                    id='checkAll'
                    v-model='allSelected'
                    :indeterminate.prop='indeterminate'
                    type='checkbox'
                    @change='selectAll'
                  />
                  <span
                    :class='{ indeterminate: indeterminate === true }'
                    class='checkmark'
                  ></span>
                </label>
              </span>
          </th>
          <th class='w-55 hidden-in-mobile'></th>
          <th class='w-100px'>Home page priority</th>
          <th class='w-100px'>Instagram</th>
          <th class='w-140'>Name</th>
          <th class='w-140 hidden-in-mobile hidden-in-tabled'>Email</th>
          <th class='w-140 hidden-in-mobile'>Phone</th>
          <th class='hidden-in-mobile hidden-in-tabled'>Genre</th>
          <th></th>
        </tr>
        </thead>
        <tbody>
        <tr v-for='(user, index) in listItems' :key='index'>
          <td class='cb-td-with-start'>
              <span
                class='checkbox-wrapper cb--with-start'
              ><label
              ><input
                v-model='selectedItems'
                :value='user.id'
                type='checkbox'
                @change='select'
              /><span class='checkmark'></span
              ></label>
                <img
                  :class="{ 'not-favorite': !user.isFeatured }"
                  class='icon-w-a'
                  src='/images/star.svg'
                  style='cursor: pointer'
                  @click='toggleFeatured(user)'
                />
              </span>
          </td>
          <td class='hidden-in-mobile'>
            <img :src='user.profile.image'/>
          </td>
          <td>
            <input v-if='user.isFeatured' v-model='user.priority' class='hp-priority' type='number'/>
            <button v-if='user.isFeatured' @click='setPriority(user)'>Save</button>
          </td>
          <td class='width-fix'>
            <div class='width-fix-content'>
              <a
                v-if='user.profile.instagram_handle != null'
                :href="'https://www.instagram.com/' + user.profile.instagram_handle"
                target='_blank'
              >
                {{ '@' + user.profile.instagram_handle }}
              </a>
            </div>
          </td>
          <td class='width-fix'>
            <div class='width-fix-content'>
              <a :href="'/profile/' + user.id" class='link-to-profile'>{{ user.full_name }}</a>
            </div>
          </td>

          <td class='width-fix hidden-in-tabled hidden-in-mobile'>
            <div class='width-fix-content'>{{ user.email }}</div>
          </td>

          <td class='hidden-in-mobile'>{{ user.profile.mobile_phone }}</td>
          <td
            class='cusotm-w hidden-in-tabled hidden-in-mobile'
            v-html='getClientGenresList(user.genres)'
          ></td>
          <td class='action'>
            <div class='dropdown'>
              <button
                class='btn btn-left'
                @click.prevent='showUserPublicProfile(user.id)'
              >
                View
              </button>
              <button
                aria-expanded='false'
                aria-haspopup='true'
                class='btn btn-outline-secondary dropdown-toggle'
                data-toggle='dropdown'
                type='button'
              ></button>
              <div aria-labelledby='dropdownMenuButton' class='dropdown-menu'>
                <a
                  :href="'/profile/edit/' + user.id" class='dropdown-item'
                >Edit profile</a
                >
                <a
                  class='dropdown-item'
                  href='#'
                  @click.prevent='notifyInstructor(user.id)'
                >Contact</a
                >
                <a
                  v-if="user.status != 'on_review'"
                  :href="'/backend/lessons?instructor=' + user.id"
                  class='dropdown-item'
                >Lessons</a
                >
                <a
                  v-if="user.status != 'on_review'"
                  :href="'/backend/instructors?invited_by=' + user.id"
                  class='dropdown-item'
                >Invited instructors</a
                >
                <a
                  v-if="user.status == 'on_review'"
                  class='dropdown-item'
                  href='#'
                  @click.prevent='approveUser(user)'
                >Approve</a
                >
                <a
                  v-if="user.status == 'on_review'"
                  class='dropdown-item'
                  href='#'
                  @click.prevent='denyUser(user)'
                >Deny</a
                >
                <a
                  v-if="user.status == 'approved'"
                  class='dropdown-item'
                  href='#'
                  @click.prevent='sendFinishRegistrationReminder(user)'
                >Resend Reminder</a
                >
                <a
                  v-if="
                      user.status != 'blocked' && user.status != 'on_review'
                    "
                  class='dropdown-item'
                  href='#'
                  @click.prevent='loginAsUser(user)'
                >Login</a
                >
                <a
                  v-if="
                      user.status != 'blocked' && user.status != 'on_review'
                    "
                  class='dropdown-item red'
                  href='#'
                  @click.prevent='blockUser(user)'
                >Suspend</a
                >
                <a
                  v-if="
                      user.status != 'blocked' && user.status != 'on_review'
                    "
                  class='dropdown-item red'
                  href='#'
                  @click.prevent='deleteUser(user)'
                >Delete</a
                >
              </div>
            </div>
          </td>
        </tr>
        </tbody>
      </table>

    </div>
    <div v-if='pagination.total_pages > 1' class='row justify-content-center'>
      <div class='col-6 select-show'>
        <label>Show</label>
        <select v-model='pagination.per_page' @change='onChangePerPage'>
          <option value='25'>25</option>
          <option value='50'>50</option>
          <option value='100'>100</option>
        </select>
      </div>
      <div
        class='col-6 d-flex justify-content-end align-items-center custom-pag'
      >
        <span
        >{{ firstListItemNumber }}-{{ lastListItemNumber }} of
          {{ pagination.total }}</span
        >
        <paginate
          :click-handler='paginatorClickCallback'
          :container-class="'clients-pagination'"
          :force-page='pagination.current_page'
          :next-text="'Next'"
          :page-count='pagination.total_pages'
          :prev-text="'Prev'"
        >
        </paginate>
      </div>
    </div>
  </div>
</template>

<script>
import siteAPI from '../../mixins/siteAPI.js'
import skillectiveHelper from '../../mixins/skillectiveHelper.js'
import Paginate from 'vuejs-paginate'
import axios from 'axios'

export default {
  mixins: [siteAPI, skillectiveHelper],
  props: {
    instructors: null,
    instructorsMeta: {},
    invitedByInstagramHandle: null
  },
  components: {
    Paginate
  },
  data() {
    return {
      listItems: [],
      selectedItems: [],
      searchString: '',
      invitedBy: null,
      showOnly: 'active',
      allSelected: false,
      indeterminate: false,
      pagination: {
        total: 0,
        total_pages: 0,
        current_page: 0,
        per_page: 0
      },
      reloadUsers: true,
      loginAsUserId: null,
      csrf: null,
      invitedMessage: {
        success: null,
        error: null
      }
    }
  },
  methods: {
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
    },
    selectAll() {
      this.selectedItems = []
      if (this.allSelected) {
        this.indeterminate = false
        this.listItems.forEach(user => {
          this.isResendInvite
            ? this.selectedItems.push(user.email)
            : this.selectedItems.push(user.id)
        })
      }
    },
    toggleFeatured: function (user) {
      this.apiPut(`/api/admin/instructors/featured/${ user.id }`)
    },
    setPriority: function (user) {
      this.apiPut(`/api/admin/instructors/priority/${ user.id }`, {
        priority: user.priority
      })
    },
    notifyInstructor(client) {
      var temp = []
      temp.push({ id: client })
      this.$root.$emit('initNotificationsForm', temp)
    },
    select: function () {
      if (this.listItems.length === this.selectedItems.length) {
        this.allSelected = 1
        this.indeterminate = false
      } else if (this.selectedItems.length === 0) {
        this.allSelected = 0
        this.indeterminate = false
      } else {
        this.allSelected = 0
        this.indeterminate = true
      }
    },
    toggleShowOnly(status) {
      this.showOnly = status
      this.allSelected = 0
      this.selectedItems = []
      this.invitedMessage = {}
      this.pagination.current_page = 1
      this.getUsers()
    },
    paginatorClickCallback(pageNum) {
      this.pagination.current_page = pageNum
      this.getUsers()
    },
    onChangePerPage() {
      this.pagination.current_page = 1
      Cookies.set('adminInstructorsPerPage', this.pagination.per_page)
      this.getUsers()
    },
    blockManyUsers() {
      this.apiPost('/api/admin/instructors/block', {
        users: this.selectedItems
      })
    },
    loginAsUser(user) {
      this.loginAsUserId = user.id
      this.csrf = document.head.querySelector(
        'meta[name="csrf-token"]'
      ).content
      setTimeout(() => {
        this.$refs.loginAsForm.submit()
      }, 50)
    },
    notifyManyUsers() {
      this.$root.$emit('initNotificationsForm', this.selectedItems)
    },
    blockUser(user) {
      this.apiDelete('/api/admin/users/' + user.id)
    },
    deleteUser(user) {
      this.apiDelete('/api/admin/users/delete/' + user.id)
    },
    approveUser(user) {
      this.apiPut('/api/admin/instructor/approve/' + user.id)
    },
    denyUser(user) {
      this.apiPut('/api/admin/instructor/deny/' + user.id)
    },
    sendFinishRegistrationReminder(user) {
      this.reloadUsers = false
      this.apiPost(
        '/api/admin/instructor/resend-registration-reminder/' + user.id
      )
    },
    notifyUser(user) {
      var temp = []
      temp.push({ id: user })
      this.$root.$emit('initNotificationsForm', temp)
    },
    searchUsers() {
      this.pagination.current_page = 1
      this.getUsers()
    },
    getUsers() {
      let queryParams = {}
      queryParams.status = this.showOnly

      if (this.pagination.current_page != undefined)
        queryParams.page = this.pagination.current_page
      else queryParams.page = 1
      if (this.searchString != '') queryParams.s = this.searchString

      if (this.invitedBy != null) queryParams.invited_by = this.invitedBy
      this.updateUrlQueryParams(queryParams)

      if (this.showOnly === 'invited') {
        this.apiGet('/api/admin/invited', {
          params: queryParams
        })
      } else {
        this.apiGet('/api/admin/instructors', {
          params: queryParams
        })
      }
    },
    componentHandleGetResponse(responseData) {
      this.listItems = responseData.data.data
      this.selectedItems = []
      if (
        responseData.data.meta != undefined &&
        responseData.data.meta.pagination != undefined
      ) {
        this.pagination.count = responseData.data.meta.pagination.count
        this.pagination.total = responseData.data.meta.pagination.total
        this.pagination.total_pages =
          responseData.data.meta.pagination.total_pages
        this.pagination.current_page =
          responseData.data.meta.pagination.current_page
        this.pagination.per_page = responseData.data.meta.pagination.per_page
      }
      this.allSelected = false
      this.indeterminate = false
    },
    componentHandleDeleteResponse(responseData) {
      this.getUsers()
    },
    componentHandlePutResponse(responseData) {
      this.getUsers()
    },
    componentHandlePostResponse(responseData) {
      if (this.reloadUsers === true) {
        this.getUsers()
      }
      this.reloadUsers = true
    },
    showUserPublicProfile(userId) {
      window.location = '/profile/' + userId
    },
    async resendInvite() {
      try {
        const res = await axios.post('/api/admin/invite-resend-instructors', {
          contacts_to_invite: this.selectedItems.toString()
        })
        this.invitedMessage.success = res.data.message
        this.selectedItems = []
        this.allSelected = false
        this.indeterminate = false
      } catch (error) {
        this.invitedMessage.error = error
      }
    }
  },
  created: function () {
    if (
      this.instructorsMeta != undefined &&
      this.instructorsMeta.pagination != undefined
    ) {
      this.pagination.count = this.instructorsMeta.pagination.count
      this.pagination.total = this.instructorsMeta.pagination.total
      this.pagination.total_pages = this.instructorsMeta.pagination.total_pages
      this.pagination.current_page =
        this.instructorsMeta.pagination.current_page
      this.pagination.per_page = this.instructorsMeta.pagination.per_page
    }
    if (this.getUrlParameter('s'))
      this.searchString = this.getUrlParameter('s')
    if (this.getUrlParameter('status'))
      this.showOnly = this.getUrlParameter('status')
    if (this.getUrlParameter('invited_by'))
      this.invitedBy = this.getUrlParameter('invited_by')

    this.debouncedGetUsers = _.debounce(this.searchUsers, 500)
    this.getUsers()
  },
  watch: {
    searchString: function (newSearchString, oldSearchString) {
      this.debouncedGetUsers()
    }
  },
  computed: {
    isResendInvite() {
      return this.showOnly === 'invited'
    },
    firstListItemNumber: function () {
      return (
        this.pagination.current_page * this.pagination.per_page -
        this.pagination.per_page +
        1
      )
    },
    lastListItemNumber: function () {
      if (this.pagination.count == this.pagination.per_page) {
        return this.firstListItemNumber + this.pagination.per_page - 1
      } else {
        return this.firstListItemNumber + this.pagination.count - 1
      }
    }
  }
}
</script>

<style lang='scss' scoped>
.invite-buttons {
  display: flex;
  align-items: center;
}
.table-page .table-invited {
  th {
    text-align: left !important;
    padding-left: 15px;
  }
  tr {
    border-top: 1px solid #ddd;
  }
  td {
    text-align: left !important;
    padding: 15px !important;
  }
}
.btn-green {
  border: none;
  width: 100% !important;
}
.btn-green:disabled {
  background: #b5b5b5 !important;
}
.email-header {
  padding-left: 70px !important;
}
</style>