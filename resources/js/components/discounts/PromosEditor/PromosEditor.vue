<template>
  <div class='promos-editor'>
    <confirmation-popup ref='confirmationPopup'/>
    <div
      v-if="viewMode === 'list'"
      class='promos-editor__header'
    >
      <h2>Promo code</h2>
      <button @click="setViewMode('create')">Create Promo Code</button>
    </div>
    <div
      v-if="isLoading && viewMode === 'list'"
      class='discounts-editor__item'
    >
      <anim-loader/>
    </div>
    <div
      v-if='!computedPromos.length && !isLoading'
      class='promos-editor__item'
    >
      <div class='promos-editor__empty'>
        <img
          alt=''
          src='/images/coupon-large-icon.svg'
        />
        <span>You have not created any promo codes.</span>
        <button @click="setViewMode('create')">Create Promo Code</button>
      </div>
    </div>
    <div
      v-for='(promo, promoIndex) in computedPromos'
      :key='promoIndex'
      class='promos-editor__item'
    >
      <div class='promos-editor__item-header'>
        <h3 v-if="viewMode === 'create'">Create Promo code</h3>
        <h3 v-else-if="viewMode === 'edit'">Edit Promo code</h3>
        <h3 v-else-if='promo.title'>{{ promo.title }}</h3>
        <h3 v-else>Promo code {{ promoIndex + 1 }}</h3>
        <options-menu
          v-if="viewMode === 'list'"
          :options='optionsMenuItems'
          squared
          @menu-item-clicked='optionsMenuItemClicked($event, promo.id)'
        />
      </div>
      <div class='promos-editor__item-row'>
        <div class='promos-editor__item-col discounts-editor__item-col--full'>
          <span class='promos-editor__item-col-title'>Promo code</span>
          <copy-input
            :readonly="viewMode === 'list'"
            :value='promo.name'
            @promo-changed='promoName => tmpPromo.name = promoName'
          />
          <field-errors v-model='errors.name'/>
        </div>
      </div>
      <div class='promos-editor__item-row'>
        <div class='promos-editor__item-col'>
          <span class='promos-editor__item-col-title'>Title</span>
          <input
            v-model='promo.title'
            :disabled="viewMode === 'list'"
          />
          <field-errors v-model='errors.title'/>
        </div>
        <div class='promos-editor__item-col promos-editor__item-col--full'>
          <span class='promos-editor__item-col-title'>Lesson type</span>
          <select
            v-model='promo.lesson_type'
            :disabled="viewMode === 'list'"
          >
            <option
              v-for='(lessonType, lessonTypeIndex) in promoOptions.lessonTypes'
              :key='lessonTypeIndex'
              :value='lessonType.value'
            >
              {{ lessonType.label }}
            </option>
          </select>
          <field-errors v-model='errors.lesson_type'/>
        </div>
      </div>
      <div class='promos-editor__item-row'>
        <div class='promos-editor__item-col'>
          <span class='promos-editor__item-col-title'>Maximum number of times of use</span>
          <disc-number-input
            :disabled="viewMode === 'list'"
            :value='promo.users_count'
            @value-changed='maxCountChanged($event)'
          />
          <field-errors v-model='errors.users_count'/>
        </div>
        <div class='promos-editor__item-col promos-editor__item-col--full'>
          <span class='promos-editor__item-col-title'>Discount type</span>
          <select
            v-model='promo.discount_type'
            :disabled="viewMode === 'list'"
          >
            <option
              v-for='( discountType, discountTypeIndex) in promoOptions.discountTypes'
              :key='discountTypeIndex'
              :value='discountType.value'
            >
              {{ discountType.label }}
            </option>
          </select>

          <field-errors v-model='errors.discount_type'/>
        </div>
        <div class='promos-editor__item-col promos-editor__item-col--short'>
          <span class='promos-editor__item-col-title'>Discount</span>
          <price-input
            v-model='promo.discount'
            :disabled="viewMode === 'list'"
            :percent-mode="promo.discount_type === 'percent'"
          />
          <field-errors v-model='errors.discount'/>
        </div>
      </div>
      <div class='promos-editor__item-row'>
        <div class='promos-editor__item-col'>
          <span class='promos-editor__item-col-title'>Use discount with other promo codes or discounts in the cart</span>
          <select
            v-model='promo.used_with_other_discounts'
            :disabled="viewMode === 'list'"
          >
            <option
              v-for='(multi, multiIndex) in promoOptions.multiPromos'
              :key='multiIndex'
              :value='multi.value'
            >
              {{ multi.label }}
            </option>
          </select>
          <field-errors v-model='errors.used_with_other_discounts'/>
        </div>
        <div class='promos-editor__item-col'>
          <span class='promos-editor__item-col-title'>Start date</span>
          <date-picker
            v-model='promo.start'
            :disabled="viewMode === 'list'"
          />
          <field-errors v-model='errors.start'/>
        </div>
        <div class='promos-editor__item-col'>
          <span class='promos-editor__item-col-title'>Finish date</span>
          <date-picker
            v-model='promo.finish'
            :disabled="viewMode === 'list'"
          />
          <field-errors v-model='errors.finish'/>
        </div>
      </div>
      <div
        v-if="viewMode === 'create' || viewMode === 'edit'"
        class='promos-editor__notice'
      >
        <div class='promos-editor__item-header'>
          <h3>Client Notice</h3>
        </div>
        <div class='promos-editor__item-row'>
          <div class='promos-editor__item-col'>
            <span class='promos-editor__item-col-title'>Method of notification</span>
            <select-with-search
              :options='promoOptions.notifMethods'
              :value='notice.notifMethod'
              @value-changed='notifMethodChanged($event)'
            />
          </div>
          <div class='promos-editor__item-col promos-editor__item-col--full'>
            <input
              v-model='notice.searchText'
              placeholder='Search client'
            />
          </div>
        </div>
        <div class='promos-editor__notice-table'>
          <client-table
            ref='clientTable'
            @checked-clients-changed='checkedClientsChanged($event)'
          />
        </div>
      </div>
      <div
        v-if="viewMode === 'list'"
        class='promos-editor__item-row'
      >
        <div class='promos-editor__item-col'>
          <ul class='promos-editor__item-counters'>
            <li
              v-for='(counter, counterIndex) in promo.counters'
              :key='counterIndex'
              class='promos-editor__item-counter'
            >
              <span class='promos-editor__item-counter-name'>{{ counters[counterIndex] }}</span>
              <span class='promos-editor__item-counter-value'>{{ counter }}</span>
            </li>
          </ul>
        </div>
        <div class='promos-editor__item-col promos-editor__item-col--full promos-editor__item-status'>
          <span
            :class="{
              'promos-editor__item-status-label': true,
              'promos-editor__item-status-label--orange': calcDaysRemaining(promo.start,promo.finish).orange,
            }"
          >
            {{ calcDaysRemaining(promo.start, promo.finish).message }}
          </span>
        </div>
      </div>
      <div
        v-else
        class='promos-editor__item-buttons'
      >
        <button
          class='promos-editor__item-cancel'
          @click="setViewMode('list')"
        >
          Cancel
        </button>
        <loader-button
          v-if="viewMode === 'create'"
          :isLoading='isLoading'
          text='Create Promo Code'
          @click='createPromo'
        />
        <loader-button
          v-else
          :isLoading='isLoading'
          text='Save'
          @click='savePromo'
        />
      </div>
      <field-errors
        v-model='errors.message'
        align='right'
      />
    </div>
  </div>
</template>

<script>
import FieldErrors from '../../instructor/FieldErrors/FieldErrors.vue'
import DatePicker from '../DatePicker/DatePicker.vue'
import AnimLoader from '../../cart/AnimLoader/AnimLoader.vue'
import OptionsMenu from '../../student/OptionsMenu/OptionsMenu.vue'
import SelectWithSearch from '../../student/SelectWithSearch/SelectWithSearch.vue'
import PriceInput from '../../lessons/PriceInput/PriceInput.vue'
import LoaderButton from '../../cart/LoaderButton/LoaderButton.vue'
import ConfirmationPopup from '../../instructor/ConfirmationPopup/ConfirmationPopup.vue'
import CopyInput from '../CopyInput/CopyInput.vue'
import ClientTable from '../ClientTable/ClientTable.vue'
import instructorService from '../../../services/instructorService'
import discountPromoService from '../../../services/discountPromoService'
import DiscNumberInput from '../DiscNumberInput/DiscNumberInput.vue'

export default {
  name: 'PromosEditor',
  components: {
    OptionsMenu,
    SelectWithSearch,
    PriceInput,
    LoaderButton,
    ConfirmationPopup,
    CopyInput,
    ClientTable,
    AnimLoader,
    DatePicker,
    FieldErrors,
    DiscNumberInput
  },
  data() {
    return {
      isLoading: true,
      viewMode: 'list',
      optionsMenuItems: [
        { text: 'Edit promo code' },
        { text: 'Delete promo code', red: true }
      ],
      notice: {
        notifMethod: 'email',
        searchText: ''
      },
      checkedClients: [],
      counters: [
        'Number of students notified:',
        'Used a promo code:',
        'Not used:'
      ],
      tmpPromo: null,
      promoInEdit: 0,
      promos: [],
      promoOptions: {
        lessonTypes: [
          {
            value: 'all',
            label: 'All Lessons'
          },
          {
            value: 'virtual',
            label: 'Virtual'
          },
          {
            value: 'pre-recorded',
            label: 'Pre-Recorded'
          },
          {
            value: 'in-person',
            label: 'In-Person'
          }
        ],
        discountTypes: [
          {
            value: 'fixed-amount',
            label: 'Fixed amount'
          },
          {
            value: 'percent',
            label: 'Percent'
          }
        ],
        multiPromos: [
          {
            value: 1,
            label: 'Yes'
          },
          {
            value: 0,
            label: 'No'
          }
        ],
        notifMethods: [
          {
            value: 'email',
            label: 'Email'
          }
        ]
      },
      errors: {
        message: [],
        title: [],
        used_with_other_discounts: [],
        discount: [],
        lesson_type: [],
        users_count: [],
        discount_type: [],
        finish: [],
        start: [],
        name: []
      }
    }
  },
  props: {
    instructorId: {
      type: Number
    }
  },
  watch: {
    viewMode(newValue) {
      if (newValue === 'list') {
        this.errors = {
          message: [],
          title: [],
          used_with_other_discounts: [],
          discount: [],
          lesson_type: [],
          users_count: [],
          discount_type: [],
          finish: [],
          start: [],
          name: []
        }
      }
    }
  },
  computed: {
    computedPromos() {
      return this.viewMode !== 'list' ? [this.tmpPromo] : this.promos
    }
  },
  async created() {
    await this.loadPromos()
  },
  methods: {
    calcDaysRemaining(start, finish) {
      const dateNow = moment()
      const dateStart = moment(start)
      const dateFinish = moment(finish)

      if (dateStart.isAfter(dateNow)) {
        return {
          message: `Will start in ${ dateStart.diff(dateNow, 'days') + 1 } days`,
          orange: true
        }
      } else if (dateNow.isAfter(dateFinish)) {
        return {
          message: `Ended ${ dateNow.diff(dateFinish, 'days') + 1 } days ago`,
          orange: true
        }
      } else {
        return {
          message: `Will be active for another ${
            dateFinish.diff(dateNow, 'days') + 1
          } days`,
          orange: false
        }
      }
    },
    async loadPromos() {
      this.promos = await discountPromoService.getPromos(this.instructorId)
      this.isLoading = false
    },
    generatePromoId() {
      let text = ''
      const possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'
      for (let i = 0; i < 11; i++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length))
      }
      return text
    },
    confirmDelete(text, action) {
      this.$refs.confirmationPopup.showConfirm(text, () => action())
    },
    optionsMenuItemClicked(menuItemIndex, id) {
      if (menuItemIndex === 0) {
        this.promoInEdit = id
        this.tmpPromo = this.promos.find(item => item.id === id)
        this.setViewMode('edit')
      } else if (menuItemIndex === 1) {
        this.removePromo(id)
        this.setViewMode('list')
      }
    },
    async removePromo(id) {
      this.confirmDelete(
        'Are you sure you want to delete this promo code?',
        async () => {
          const result = await instructorService.deletePromo(id)
          if (result.errors) {
            this.errors = result.errors
            this.errors.message = [result.message]
          } else {
            const deletedIndex = this.promos.findIndex(item => item.id === id)
            this.promos.splice(deletedIndex, 1)
            this.setViewMode('list')
          }
        }
      )
    },
    async savePromo() {
      this.isLoading = true
      const result = await instructorService.updatePromo(
        this.tmpPromo,
        this.tmpPromo.id,
        this.checkedClients
      )
      this.isLoading = false

      if (result.errors) {
        this.errors = result.errors
        this.errors.message = [result.message]
      } else {
        const promoInEditIndex = this.promos.findIndex(item => item.id === this.tmpPromo
        )
        this.promos[promoInEditIndex] = this.tmpPromo
        this.setViewMode('list')
      }
    },
    async createPromo() {
      this.isLoading = true
      const result = await instructorService.createPromo(this.tmpPromo, this.checkedClients)
      this.isLoading = false

      if (result.errors) {
        this.errors = result.errors
        this.errors.message = [result.message]
      } else {
        this.promos.push(this.tmpPromo)
        this.tmpPromo = null
        this.setViewMode('list')
      }
    },
    checkedClientsChanged(checkedClients) {
      this.checkedClients = checkedClients
    },
    setViewMode(mode) {
      if (mode === 'create') {
        this.tmpPromo = {
          name: this.generatePromoId(),
          title: '',
          lesson_type: 'all',
          users_count: 1,
          discount: '',
          discount_type: 'percent',
          used_with_other_discounts: true,
          start: '',
          finish: '',
          counters: [0, 0, 0],
          daysRemaining: 0
        }
      }
      this.viewMode = mode
    },
    lessonTypeChanged(event) {
      console.log(event)
      // if (this.tmpPromo) {
      //   this.tmpPromo.lesson_type = event.value
      // }
    },
    discountTypeChanged(event) {
      if (this.tmpPromo) {
        this.tmpPromo.discount_type = event.value
      }
    },
    maxCountChanged(event) {
      if (this.tmpPromo) {
        this.tmpPromo.maxCount = event
      }
    },
    notifMethodChanged(event) {
      this.notice.notifMethod = event.value
    }
  }
}
</script>

<style
  lang='scss'
  scoped
>
@import '../DiscountsEditor/DiscountsEditor.scss';
</style>
