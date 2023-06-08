import axios from 'axios'
import guestCartHelper from '../helpers/guestCartHelper'

const cartService = {
  async fetchCartItems() {
    const response = await axios
      .get(
        '/api/cart',
        {
          params: {
            guest_cart: guestCartHelper.getProductsString()
          }
        }
      ).catch(e => console.log(e))
    return response.data.data.data
  },
  async addItemToCartAtStart(data, guestMode) {
    if (guestMode) {
      return guestCartHelper.addProduct({
        lesson_id: data.lessonId,
        description: data.specialRequest,
        isPreRecorded: data.isPreRecorded
      })
    } else {
      return await axios
        .post('/api/cart', {
          lesson_id: data.lessonId,
          description: data.specialRequest,
          isPreRecorded: data.isPreRecorded
        }).catch(({ response }) => {
          return { message: response.data, isError: true }
        })
    }
  },
  async removeItemFromCart(id, guestMode) {
    guestMode || guestCartHelper.isLessonInGuestCart(id)
      ? guestCartHelper.removeProduct(id)
      : await axios.delete('/api/cart/' + id).catch(e => console.log(e))
  },
  async checkCartDotNeeded(guestMode) {
    let needed = false
    if (guestMode) {
      needed = !guestCartHelper.isEmpty()
    } else {
      needed = await axios.get(
        '/api/cart/has-items',
        {
          params: {
            guest_cart: guestCartHelper.getProductsString()
          }
        }
      ).catch(e => console.log(e))
      needed = needed.data.data
      // guestCartHelper.clearProducts();
    }
    return needed
  },
  async fetchCartTotal(guestMode) {
    if (guestMode) {
      const response = await axios.get(
        '/api/cart/total',
        {
          params: {
            guest_cart: guestCartHelper.getProductsString(),
            promo_codes: guestCartHelper.getPromosString()
          }
        }
      ).catch(e => console.log(e))
      return response.data.data
    } else {
      const response = await axios.get(
        '/api/cart/total',
        {
          params: {
            guest_cart: guestCartHelper.getProductsString(),
            promo_codes: guestCartHelper.getPromosString()
          }
        }
      ).catch(e => console.log(e))
      return response.data.data
    }
  }
}

export default cartService
