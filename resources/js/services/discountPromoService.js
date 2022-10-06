import axios from 'axios'
import urlHelper from '../helpers/urlHelper'

const discountPromoService = {
  async getDiscounts() {
    const response = await axios
      .get('/api/instructor/discount')
      .catch(e => {
        console.log(e)
      })
    return response.data.data
  },
  async getPromos() {
    const response = await axios
      .get('/api/instructor/promo')
      .catch(e => {
        console.log(e)
      })
    return response.data.data
  },
  async getInstructorClients(params) {
    const response = await axios
      .get(urlHelper.buildRequestParamsStr('/api/instructor/clients', params))
      .catch(e => {
        console.log(e)
      })
    return {
      clients: response.data.data.data,
      pagination: {
        currentPage: response.data.data.meta.pagination.current_page,
        pageCount: response.data.data.meta.pagination.total_pages,
        totalClients: response.data.data.meta.pagination.total,
        clientsPerPage: response.data.data.meta.pagination.per_page,
        clientsCount: response.data.data.meta.pagination.count
      }
    }
  }
}

export default discountPromoService
