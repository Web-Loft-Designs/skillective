<template>
  <div class="discounts-editor">
    <confirmation-popup ref="confirmationPopup" />

    <div class="discounts-editor__header" v-if="viewMode == 'list'">
      <h2>Discount</h2>
      <button @click="setViewMode('create')">Create Discount</button>
    </div>

    <div class="discounts-editor__item" v-if="isLoading && viewMode == 'list'">
      <anim-loader />
    </div>

    <div
      class="discounts-editor__item"
      v-if="!computedDiscounts.length && !isLoading"
    >
      <div class="discounts-editor__empty">
        <img src="/images/percent-large-icon.svg" alt="" />
        <span>You have not created any discounts.</span>
        <button @click="setViewMode('create')">Create Discount</button>
      </div>
    </div>

    <div
      class="discounts-editor__item"
      v-for="(discount, discountIndex) in computedDiscounts"
      :key="discountIndex"
    >
      <div class="discounts-editor__item-header">
        <h3 v-if="viewMode == 'create'">Create Discount</h3>
        <h3 v-else-if="viewMode == 'edit'">Edit Discount</h3>
        <h3 v-else-if="discount.title">{{ discount.title }}</h3>
        <h3 v-else>Discount {{ discountIndex + 1 }}</h3>
        <options-menu
          v-if="viewMode == 'list'"
          :options="optionsMenuItems"
          @menu-item-clicked="optionsMenuItemClicked($event, discount.id)"
          squared
        />
      </div>
      <div class="discounts-editor__item-row">
        <div class="discounts-editor__item-col">
          <span class="discounts-editor__item-col-title">Title</span>
          <input :disabled="viewMode == 'list'" v-model="discount.title" />
          <field-errors v-model="errors.title" />
        </div>
        <div
          class="discounts-editor__item-col discounts-editor__item-col--full"
        >
          <span class="discounts-editor__item-col-title">Lesson type</span>
          <select :disabled="viewMode == 'list'" v-model="discount.lesson_type">
            <option
              v-for="(
                lessonType, lessonTypeIndex
              ) in discountOptions.lessonTypes"
              :key="lessonTypeIndex"
              :value="lessonType.value"
            >
              {{ lessonType.label }}
            </option>
          </select>
          <field-errors v-model="errors.lesson_type" />
        </div>
      </div>
      <div class="discounts-editor__item-row">
        <div class="discounts-editor__item-col">
          <span class="discounts-editor__item-col-title"
            >Number of lessons to participate in the discount</span
          >
          <disc-number-input
            :disabled="viewMode == 'list'"
            v-model="discount.lessons_for_apply"
          />
          <field-errors v-model="errors.lessons_for_apply" />
        </div>
        <div
          class="discounts-editor__item-col discounts-editor__item-col--full"
        >
          <span class="discounts-editor__item-col-title">Discount type</span>
          <select
            :disabled="viewMode == 'list'"
            v-model="discount.discount_type"
          >
            <option
              v-for="(
                discountType, discountTypeIndex
              ) in discountOptions.discountTypes"
              :key="discountTypeIndex"
              :value="discountType.value"
            >
              {{ discountType.label }}
            </option>
          </select>
          <field-errors v-model="errors.discount_type" />
        </div>
        <div
          class="discounts-editor__item-col discounts-editor__item-col--short"
        >
          <span class="discounts-editor__item-col-title">Discount</span>
          <price-input
            v-model="discount.discount"
            :disabled="viewMode == 'list'"
            :percent-mode="discount.discount_type == 'percent'"
          />
          <field-errors v-model="errors.discount" />
        </div>
      </div>
      <div class="discounts-editor__item-row">
        <div class="discounts-editor__item-col">
          <span class="discounts-editor__item-col-title">Start date</span>
          <date-picker
            :disabled="viewMode == 'list'"
            v-model="discount.start"
          />
          <field-errors v-model="errors.start" />
        </div>
        <div class="discounts-editor__item-col">
          <span class="discounts-editor__item-col-title">Finish date</span>
          <date-picker
            :disabled="viewMode == 'list'"
            v-model="discount.finish"
          />
          <field-errors v-model="errors.finish" />
        </div>
      </div>
      <div class="discounts-editor__item-row">
        <div class="discounts-editor__item-col">
          <span class="discounts-editor__item-col-title"
            >Maximum number of times this code can be used</span
          >
          <disc-number-input
            :disabled="viewMode == 'list'"
            v-model="discount.users_count"
          />
          <field-errors v-model="errors.users_count" />
        </div>
        <div
          class="discounts-editor__item-col discounts-editor__item-col--full"
        >
          <span class="discounts-editor__item-col-title"
            >Use discount with other promo codes or discounts in the cart</span
          >
          <select
            :disabled="viewMode == 'list'"
            v-model="discount.used_with_other_discounts"
          >
            <option
              v-for="(multi, multiIndex) in discountOptions.multiPromos"
              :key="multiIndex"
              :value="multi.value"
            >
              {{ multi.label }}
            </option>
          </select>
          <field-errors v-model="errors.used_with_other_discounts" />
        </div>
      </div>
      <div class="discounts-editor__item-row" v-if="viewMode == 'list'">
        <div class="discounts-editor__item-col">
          <ul class="discounts-editor__item-counters">
            <li
              v-for="(counter, counterIndex) in discount.counters"
              :key="counterIndex"
              class="discounts-editor__item-counter"
            >
              <span class="discounts-editor__item-counter-name">{{
                counters[counterIndex]
              }}</span>
              <span class="discounts-editor__item-counter-value">{{
                counter
              }}</span>
            </li>
          </ul>
        </div>
        <div
          class="
            discounts-editor__item-col
            discounts-editor__item-col--full
            discounts-editor__item-status
          "
        >
          <span
            :class="{
              'discounts-editor__item-status-label': true,
              'discounts-editor__item-status-label--orange': calcDaysRemaining(
                discount.start,
                discount.finish
              ).orange,
            }"
            >{{
              calcDaysRemaining(discount.start, discount.finish).message
            }}</span
          >
        </div>
      </div>
      <div class="discounts-editor__item-buttons" v-else>
        <button
          class="discounts-editor__item-cancel"
          @click="setViewMode('list')"
        >
          Cancel
        </button>
        <loader-button
          v-if="viewMode == 'create'"
          :isLoading="isLoading"
          text="Create Discount"
          @click="createDiscount()"
        />
        <loader-button
          v-else
          :isLoading="isLoading"
          text="Save"
          @click="saveDiscount()"
        />
      </div>
      <field-errors v-model="errors.message" align="right" />
    </div>
  </div>
</template>

<script>
import FieldErrors from "../../instructor/FieldErrors/FieldErrors.vue";
import DatePicker from "../DatePicker/DatePicker.vue";
import AnimLoader from "../../cart/AnimLoader/AnimLoader.vue";
import OptionsMenu from "../../student/OptionsMenu/OptionsMenu.vue";
import PriceInput from "../../lessons/PriceInput/PriceInput.vue";
import DiscNumberInput from "../DiscNumberInput/DiscNumberInput.vue";
import LoaderButton from "../../cart/LoaderButton/LoaderButton.vue";
import ConfirmationPopup from "../../instructor/ConfirmationPopup/ConfirmationPopup.vue";
import instructorService from "../../../services/instructorService";
import discountPromoService from "../../../services/discountPromoService";

export default {
  name: "DiscountsEditor",
  components: {
    OptionsMenu,
    PriceInput,
    DiscNumberInput,
    LoaderButton,
    ConfirmationPopup,
    AnimLoader,
    DatePicker,
    FieldErrors,
  },
  data() {
    return {
      isLoading: true,
      viewMode: "list",
      optionsMenuItems: [
        { text: "Edit discount" },
        { text: "Delete discount", red: true },
      ],
      counters: ["The number of people who took advantage of the discount:"],
      tmpDiscount: null,
      discountInEdit: 0,
      discounts: [],
      discountOptions: {
        lessonTypes: [
          {
            value: "all",
            label: "All Lessons",
          },
          {
            value: "virtual",
            label: "Virtual",
          },
          {
            value: "pre-recorded",
            label: "Pre-Recorded",
          },
          {
            value: "in-person",
            label: "In-Person",
          },
        ],
        discountTypes: [
          {
            value: "fixed-amount",
            label: "Fixed amount",
          },
          {
            value: "percent",
            label: "Percent",
          },
        ],
        multiPromos: [
          {
            value: 1,
            label: "Yes",
          },
          {
            value: 0,
            label: "No",
          },
        ],
      },
      errors: {
        message: [],
        title: [],
        used_with_other_discounts: [],
        discount: [],
        lesson_type: [],
        lessons_for_apply: [],
        users_count: [],
        discount_type: [],
        finish: [],
        start: [],
      },
    };
  },
  watch: {
    viewMode(newValue) {
      if (newValue == "list") {
        this.errors = {
          message: [],
          title: [],
          used_with_other_discounts: [],
          discount: [],
          lesson_type: [],
          lessons_for_apply: [],
          users_count: [],
          discount_type: [],
          finish: [],
          start: [],
        };
      }

      if (newValue == "create") {
      }
    },
  },
  computed: {
    computedDiscounts() {
      if (this.viewMode != "list") {
        return [this.tmpDiscount];
      } else {
        return this.discounts;
      }
    },
  },
  created() {
    this.loadDiscounts();
  },
  methods: {
    calcDaysRemaining(start, finish) {
      const dateNow = moment();
      const dateStart = moment(start);
      const dateFinish = moment(finish);

      if (dateStart.isAfter(dateNow)) {
        return {
          message: `Will start in ${dateStart.diff(dateNow, "days") + 1} days`,
          orange: true,
        };
      } else if (dateNow.isAfter(dateFinish)) {
        return {
          message: `Ended ${dateNow.diff(dateFinish, "days") + 1} days ago`,
          orange: true,
        };
      } else {
        return {
          message: `Will be active for another ${
            dateFinish.diff(dateNow, "days") + 1
          } days`,
          orange: false,
        };
      }
    },
    async loadDiscounts() {
      const data = await discountPromoService.getDiscounts();
      this.discounts = data;
      this.isLoading = false;
    },
    confirmDelete(text, action) {
      this.$refs.confirmationPopup.showConfirm(text, () => {
        action();
      });
    },
    optionsMenuItemClicked(menuItemIndex, id) {
      if (menuItemIndex == 0) {
        this.discountInEdit = id;
        this.tmpDiscount = this.discounts.find((item) => item.id === id);
        console.log(this.tmpDiscount);
        this.setViewMode("edit");
      } else if (menuItemIndex == 1) {
        this.removeDiscount(id);
        this.setViewMode("list");
      }
    },
    async removeDiscount(id) {
      this.confirmDelete(
        "Are you sure you want to delete this discount?",
        async () => {
          const result = await instructorService.deleteDiscount(id);

          if (result.errors) {
            this.errors = result.errors;
            this.errors.message = [result.message];
          } else {
            const deletedIndex = this.discounts.findIndex(
              (item) => item.id === id
            );

            this.discounts.splice(deletedIndex, 1);
            this.setViewMode("list");
          }
        }
      );
    },
    async saveDiscount() {
      this.isLoading = true;
      const result = await instructorService.updateDiscount(
        this.tmpDiscount,
        this.tmpDiscount.id
      );
      this.isLoading = false;

      if (result.errors) {
        this.errors = result.errors;
        this.errors.message = [result.message];
      } else {
        const discountInEditIndex = this.discounts.findIndex(
          (item) => item.id == this.tmpDiscount.id
        );


        this.discounts[discountInEditIndex] = this.tmpDiscount;
        this.setViewMode("list");
      }
    },
    async createDiscount() {
      this.isLoading = true;
      const result = await instructorService.createDiscount(this.tmpDiscount);
      this.isLoading = false;

      if (result.errors) {
        this.errors = result.errors;
        this.errors.message = [result.message];
      } else {
        this.discounts.push(this.tmpDiscount);
        this.tmpDiscount = null;
        this.setViewMode("list");
      }
    },
    setViewMode(mode) {
      if (mode == "create") {
        this.tmpDiscount = {
          title: "",
          lesson_type: "all",
          lessons_for_apply: "",
          discount_type: "percent",
          discount: "",
          start: "",
          finish: "",
          users_count: "",
          used_with_other_discounts: 1,
        };
      }
      this.viewMode = mode;
    },
  },
};
</script>

<style lang="scss" scoped>
@import "./DiscountsEditor.scss";
</style>
