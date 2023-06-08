<template>
  <div></div>
<!--  <div id='become-an-instructor' class='become-an-instructor'>-->
<!--    <div class='become-an-instructor__container'>-->
<!--      <div class='become-an-instructor__column'>-->
<!--        <div class='become-an-instructor__content'>-->
<!--          <h3 class='become-an-instructor__heading'>Want to become an instructor?</h3>-->
<!--          <p class='become-an-instructor__text'>-->
<!--            The only way to become an instructor is to be invited by Skillective or-->
<!--            for one of the Instructors on the platform to invite you.-->
<!--          </p>-->
<!--          <form class='needs-validation' novalidate>-->
<!--            <div class='d-flex flex-wrap'>-->
<!--              <div-->
<!--                :class="{'has-error': ($v.instructor.fullName.$dirty && !$v.instructor.fullName.required)}"-->
<!--                class='form-group first-name has-feedback'-->
<!--              >-->
<!--                <input-->
<!--                  v-model='instructor.fullName'-->
<!--                  :class="{'is-invalid': ($v.instructor.fullName.$dirty && !$v.instructor.fullName.required)}"-->
<!--                  class='form-control'-->
<!--                  name='full-name'-->
<!--                  placeholder='Full name'-->
<!--                  type='text'-->
<!--                />-->
<!--                <span v-if='$v.instructor.fullName.$dirty && !$v.instructor.fullName.required'>Full name can't be empty</span>-->
<!--              </div>-->
<!--              <div-->
<!--                :class="{'has-error':-->
<!--                  ($v.instructor.email.$dirty && !$v.instructor.email.required)-->
<!--                  ||-->
<!--                  ($v.instructor.email.$dirty && !$v.instructor.email.email)-->
<!--                }"-->
<!--                class='form-group'-->
<!--              >-->
<!--                <input-->
<!--                  v-model.trim='instructor.email'-->
<!--                  :class="{'is-invalid':-->
<!--                    ($v.instructor.email.$dirty && !$v.instructor.email.required)-->
<!--                    ||-->
<!--                    ($v.instructor.email.$dirty && !$v.instructor.email.email)-->
<!--                  }"-->
<!--                  class='form-control'-->
<!--                  name='email'-->
<!--                  placeholder='Email'-->
<!--                  type='email'-->
<!--                />-->
<!--                <span v-if='$v.instructor.email.$dirty && !$v.instructor.email.required'>Email can't be empty</span>-->
<!--                <span v-else-if='$v.instructor.email.$dirty && !$v.instructor.email.email'>Invalid email format</span>-->
<!--              </div>-->
<!--              <span v-if='errorText' class='has-error pl-3'>{{ errorText }}</span>-->
<!--              <div class='form-group d-flex justify-content-center'>-->
<!--                <loader-button-->
<!--                  :isLoading='loadingBtn'-->
<!--                  style='width: 160px'-->
<!--                  text='Submit'-->
<!--                  @click='sendRequest'-->
<!--                />-->
<!--              </div>-->
<!--            </div>-->
<!--          </form>-->
<!--            <p class='become-an-instructor__large-note'>Let us know you are insterested by submitting your e-mail-->
<!--              address above!</p>-->
<!--            <p class='become-an-instructor__small-note'>** You can also contact one of the instructor already on the-->
<!--              platform and ask them to invite you</p>-->
<!--        </div>-->
<!--      </div>-->
<!--&lt;!&ndash;      <div class='become-an-instructor__column'>&ndash;&gt;-->
<!--&lt;!&ndash;        <div class='become-a-client'>&ndash;&gt;-->
<!--&lt;!&ndash;          <div class='become-a-client__content'>&ndash;&gt;-->
<!--&lt;!&ndash;            <h3 class='become-a-client__heading'>Become a Client123</h3>&ndash;&gt;-->
<!--&lt;!&ndash;            <p class='become-a-client__text'>Find, schedule, and purchase in-person, virtual or pre-recorded lessons.&ndash;&gt;-->
<!--&lt;!&ndash;              Skillective simplifies the whole process of working with talented individuals, coaches and mentors to&ndash;&gt;-->
<!--&lt;!&ndash;              improve your skills!</p>&ndash;&gt;-->
<!--&lt;!&ndash;            <a class='become-a-client__link' href='/login'>Join Now</a>&ndash;&gt;-->
<!--&lt;!&ndash;          </div>&ndash;&gt;-->
<!--&lt;!&ndash;        </div>&ndash;&gt;-->
<!--&lt;!&ndash;      </div>&ndash;&gt;-->
<!--    </div>-->
<!--  </div>-->
</template>

<script>
import { mapActions } from 'vuex'
import LoaderButton from '../../cart/LoaderButton/LoaderButton'
import { email, required } from 'vuelidate/lib/validators'
export default {
  name: 'BecomeAnInstructor',
  components: { LoaderButton },
  validations: {
    instructor: {
      fullName: { required },
      email: { email, required }
    }
  },
  data() {
    return {
      loadingBtn: false,
      instructor: {
        fullName: '',
        email: ''
      },
      errorText: ''
    }
  },
  methods: {
    ...mapActions(['sendInstructorRequest']),
    async sendRequest() {
      this.$v.$touch()
      if (!this.$v.$invalid) {
        this.loadingBtn = true
        const response = await this.sendInstructorRequest(this.instructor)
        this.errorText = response?.statusText
        this.loadingBtn = false
        if (!this.errorText) this.resetFields()
      }
    },
    resetFields() {
      for (const key in this.instructor) {
        this.instructor[key] = ''
      }
      this.$nextTick(() => {
        this.$v.$reset()
      })
    }
  }
}
</script>

<style lang='scss' scoped>
@import './BecomeAnInstructor.scss';
</style>