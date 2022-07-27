<template>
  <div class='home'>
    <div class='home__top-banner'>
      <h1 class='home__banner-heading'>
        Book In-Person, Virtual or Pre-recorded lessons from the
        instructors & influencers you Love!
      </h1>
    </div>
    <featured-instructors/>
    <become-an-instructor/>
  </div>
</template>

<script>
import BecomeAnInstructor from '../BecomeAnInstructor/BecomeAnInstructor.vue'
import FeaturedInstructors from '../FeaturedInstructors/FeaturedInstructors.vue'

export default {
  name: 'Home',
  components: {
    BecomeAnInstructor,
    FeaturedInstructors
  }
}
</script>

<style lang='scss' scoped>
@import './Home.scss';
</style>
