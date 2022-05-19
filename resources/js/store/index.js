import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'
import cart from './modules/cart'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    storeErrors: {},
    storeErrorText: '',
    instructors: []
  },
  getters: {},
  mutations: {
    SET_INSTRUCTORS: (state, data) => state.instructors = data,
    ERROR_HANDLER: (state, error) => {
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
    CLEAR_INPUT: (state) => state.storeErrors = {}
  },
  actions: {
    async getInstructors({commit}, instructorId) {
      try {
        const res = await axios.get(
          `/api/relation-instructors/${ instructorId }`)
        commit('SET_INSTRUCTORS', res.data.data)
      } catch (e) {
        commit('ERROR_HANDLER', e)
      }
    },
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
        const res = await axios.post('/api/create-to-client-list', data)
        return res.data.id
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
    async geoNotification(context, data) {
      try {
        await axios.post('/api/student/instructor/geo-notifications',data)
      } catch (e) {
        commit('ERROR_HANDLER', e)
      }
    },
    async virtualNotification(context, data) {
      try {
        await axios.post('/api/student/instructor/virtual-lesson-notifications',data)
      } catch (e) {
        commit('ERROR_HANDLER', e)
      }
    },
  },
  modules: {
    cart
  }
})

