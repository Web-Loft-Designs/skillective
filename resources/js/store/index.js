import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import cart from './modules/cart'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    storeErrors: {},
    storeErrorText: '',
  },
  getters: {},
  mutations: {
    ERROR_HANDLER: (state, error) => {
      console.log(error)
      state.storeErrors = {}
      state.storeErrorText = ''
      if (error.response !== undefined && error.response.status === 422) {
        state.storeErrors = error.response.data.errors || {}
        state.storeErrorText = error.response.data.message
      } else if (error.response !== undefined && error.response.status ===
        419) {
        state.storeErrorText = error.response.data.message ||
          'Unable to process your request. Reload the page please and try again'
      } else if (error.response !== undefined) {
        state.storeErrorText = error.response.data.message ||
          'Unable to process your request.'
      } else {
        state.storeErrorText = 'Unable to process your request'
      }
    },
    CLEAR_INPUT: (state) => state.storeErrors = {},
  },
  actions: {
    async addToClientList({commit}, instructorId) {
      try {
        await axios.post('/api/add-to-client-list',
          {instructor_id: instructorId})
      } catch (e) {
        commit('ERROR_HANDLER', e)
      }
    },
    async createToClientList({commit}, data) { // data :
      // {instructor_id,first_name,last_name,instagram_handle,zip,email,mobile_phone,newsletter}
      try {
        await axios.post('/api/create-to-client-list', data)
      } catch (e) {
        commit('ERROR_HANDLER', e)
      }
    },
    async addStudentToInstructorList({commit}, data) { // data : { studentId, [instructorId] }
      try {
        await axios.post(`/api/student/instructors`, data)
      } catch (e) {
        commit('ERROR_HANDLER', e)
      }
    },
  },
  modules: {
    cart,
  },
})

