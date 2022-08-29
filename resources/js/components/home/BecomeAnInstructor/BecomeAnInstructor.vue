<template>
  <div id='become-an-instructor' class='become-an-instructor'>
    <div class='become-an-instructor__container'>
      <div class='become-an-instructor__column'>
        <div class='become-an-instructor__content'>
          <h3 class='become-an-instructor__heading'>Become an Instructor</h3>
          <p class='become-an-instructor__text'>The only way to become an instructor is to be invited by Skillective or for one of the Instructors on the platform to invite you.</p>
          <form class='become-an-instructor__form'>
            <input placeholder='Full Name' type='text'/>
            <input placeholder='Email address' type='text'/>
            <button type='submit'>Submit</button>
          </form>
          <p class='become-an-instructor__large-note'>Let us know you are insterested by submitting your e-mail address above!</p>
          <p class='become-an-instructor__small-note'>** You can also contact one of the instructor already on the platform and ask them to invite you</p>
        </div>
      </div>
      <div class='become-an-instructor__column'>
        <div class='become-a-client'>
          <div class='become-a-client__content'>
            <h3 class='become-a-client__heading'>Become a Client</h3>
            <p class='become-a-client__text'>Find, schedule, and purchase in-person, virtual or pre-recorded lessons. Skillective simplifies the whole process of working with talented individuals, coaches and mentors to improve your skills!</p>
            <a class='become-a-client__link' href='/login'>Join Now</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BecomeAnInstructor'
}
</script>

<style lang='scss' scoped>
@import './BecomeAnInstructor.scss';
</style>