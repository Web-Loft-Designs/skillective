<template>
  <div v-if='isOpen' class='delete-confirm_popup'>
    <transition name='show'>
      <div class='delete-confirm_popup__backdrop'></div>
    </transition>
    <transition name='slidein'>
      <div class='delete-confirm_popup__modal'>
        <p>Are you sure you would like to remove this lesson from your cart?</p>
        <div class='action-buttons'>
          <button
            type='button'
            class='btn'
            @click='$emit("delete")'
          >
            Yes
          </button>
          <button
            type='button'
            class='btn btn-danger'
            @click='$emit("close-popup")'
          >
            No
          </button>
        </div>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  name: 'DeleteConfirmPopup',
  props: {
    isOpen: {
      type: Boolean,
      require: true
    }
  }
}
</script>

<style lang='scss' scoped>
.delete-confirm_popup {
  position: fixed;
  left: 0;
  top: 0;
  z-index: 1000;
  bottom: 0;
  right: 0;
  width: 100vw;
  pointer-events: none;
  &__backdrop {
    position: absolute;
    right: 0;
    top: 0;
    height: 100%;
    width: 100%;
    background: rgba(0, 0, 0, 0.65);
    pointer-events: all;
  }
  &__modal {
    pointer-events: all;
    position: absolute;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    max-width: 500px;
    width: 100%;
    background: #fff;
    display: flex;
    flex-direction: column;
    border-radius: 10px;
    padding: 30px 50px;
    p {
      text-align: center;
    }
    .action-buttons {
      display: flex;
      justify-content: center;
      margin-top: 20px;
      button {
        width: 70px;
        &:first-child {
          margin-right: 10px;
          background: linear-gradient(180deg, #11CF13 0%, #01BD00 100%);
          color: #fff;
          &:hover {
            box-shadow: 0 0 0 4px rgba(138, 218, 0, 0.5);
          }
        }
      }
    }
  }
}
</style>
