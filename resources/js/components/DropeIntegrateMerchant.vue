<template>
  <div>
    <button class='btn btn-primary btn-flat mt-3' type='submit' @click.pervetn='openModal'>
      Disconnect a PayPal account
    </button>
    <magnific-popup-modal
      ref='modal'
      :config='{closeOnBgClick:true,showCloseBtn:true,enableEscapeKey:false}'
      :show='false'
      class='modal-invade'
      @close='closeModal'
    >
      <div>
        <h1>Directions for disconnecting a PayPal account</h1>
        <ul>
          <li>Login to your PayPal account and click on your icon in the top-right corner</li>
          <li>Under "Account Settings" select "API Access"</li>
          <li>Under REST API Integration, select "Manage REST API apps and credentials"</li>
          <li>Select the app you would like to revoke and remove permissions from the platform</li>
        </ul>
        <h2 class='mt-4'>Disclaimer</h2>
        <p>Disconnecting your PayPal account will prevent you from offering PayPal services and products on your
          website. <b> Do you wish to continue? </b></p>
      </div>
      <div class='actions'>
        <button class='btn btn-primary btn-flat red-btn' type='submit' @click.pervetn='closeModal'>
          No
        </button>
        <button class='btn btn-primary ' type='submit' @click.pervetn='onSubmit'>
          Yes
        </button>
      </div>
    </magnific-popup-modal>
  </div>
</template>

<script>
import MagnificPopupModal from './external/MagnificPopupModal.vue'

export default {
  name: 'DropeIntegrateMerchant',
  components: {
    MagnificPopupModal
  },
  methods: {
    openModal() {
      this.$refs.modal.open()
    },
    closeModal() {
      this.$refs.modal.close()
    },
    async onSubmit() {
      try {
        await axios.post('/api/instructor/disable-paypal')
        this.closeModal()
        document.location.reload();
      } catch (error) {
        console.log('Something went wrong:', error)
      }


    }
  }
}
</script>

<style lang='scss' scoped>
.modal-invade {
  padding: 50px;
  text-align: center;
  max-width: 700px;
}
h1 {
  font-size: 30px;
  margin-bottom: 20px;
}
h1, h2 {
  text-align: center;
}
p {
  font-size: 19px;
}
.actions {
  display: flex;
  justify-content: center;
}
.actions .btn {
  width: 100%;
  max-width: 50px;
  background: #01bd00;
  margin: 0 10px;
}
.actions .red-btn {
  background: red;
}
</style>