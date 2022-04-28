import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)
import cart from './modules/cart'

const store = new Vuex.Store({
  state: {},
  mutations: {},
  actions: {
    async addToClientList(context, instructorId) {
      await axios
      .post('/api/add-to-client-list', {instructor_id: instructorId})
      .catch(error => this.apiHandleError(error))
    },
    async createToClientList(context, data) { // data :
  // {instructor_id,first_name,last_name,instagram_handle,zip,email,mobile_phone,newsletter}
      await axios
      .post('/api/create-to-client-list', data)
      .catch(error => this.apiHandleError(error))
    },
    async addStudentToInstructorList(context, data) { // data : { studentId, [instructorId] }
      console.log(data)
      await axios
      .post(
        `/api/student/instructors`,
        data)
      .catch(error => this.apiHandleError(error))
    },
  },
  modules: {
    cart,
  },
})

export default store

