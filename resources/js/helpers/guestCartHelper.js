const guestCartHelper = {
  getProducts() {
    let products = []
    if (localStorage.getItem('guest-cart')) {
      products = JSON.parse(localStorage.getItem('guest-cart'))
    }
    return products
  },
  getPromos() {
    let promos = []
    if (localStorage.getItem('promo-codes')) {
      promos = JSON.parse(localStorage.getItem('promo-codes'))
    }
    return promos
  },
  getPromosString() {
    return JSON.stringify(this.getPromos())
  },
  addPromos(newPromo) {
    let promos = []
    if (localStorage.getItem('promo-codes')) {
      promos = JSON.parse(localStorage.getItem('promo-codes'))
    }
    let existsPromos = promos.filter(promo => promo === newPromo)
    if (existsPromos.length) {
      return {
        success: false,
        message: 'Promo Code already applied'
      }
    } else {
      promos.push(newPromo)
      localStorage.setItem('promo-codes', JSON.stringify(promos))
      return {
        success: true
      }
    }
  },
  getProductsString() {
    return JSON.stringify(this.getProducts())
  },
  clearPromos() {
    localStorage.setItem('promo-codes', '[]')
  },
  addProduct(productData) {
    let products = []
    if (localStorage.getItem('guest-cart')) {
      products = JSON.parse(localStorage.getItem('guest-cart'))
    }
    let existsProducts = products.filter(
      product => product.lesson_id === productData.lesson_id
    )
    if (existsProducts.length) {
      return {
        success: false,
        message: 'Product already in cart'
      }
    } else {
      products.push(productData)
      localStorage.setItem('guest-cart', JSON.stringify(products))
      return {
        success: true
      }
    }
  },
  removeProduct(productId) {
    let products = []
    if (localStorage.getItem('guest-cart')) {
      products = JSON.parse(localStorage.getItem('guest-cart'))
    }
    const newProducts = products.filter(
      product => product.lesson_id !== productId
    )
    localStorage.setItem('guest-cart', JSON.stringify(newProducts))
  },
  clearProducts() {
    localStorage.setItem('guest-cart', '[]')
  },
  isEmpty() {
    return this.getProducts().length <= 0
  },
  isLessonInGuestCart(id) {
    return this.getProducts().some(el => el.lesson_id === id)
  }
}

export default guestCartHelper