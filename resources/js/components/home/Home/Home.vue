<template>
  <div class='home'>
    <div class='home__top-banner'>
      <h1 class='home__banner-heading'>
        Book In-Person, Virtual or Pre-recorded lessons from the
        instructors & influencers you Love!
      </h1>
    </div>
    <global-shop-home :can-book='canBook' :preloaded-lessons='preloadedLessons'/>
    <upcoming-lessons-near-you :can-book='canBook'/>
    <featured-instructors/>
    <become-an-instructor/>
  </div>
</template>

<script>
import BecomeAnInstructor from '../BecomeAnInstructor/BecomeAnInstructor.vue'
import FeaturedInstructors from '../FeaturedInstructors/FeaturedInstructors.vue'
import GlobalShopHome from '../GlobalShopHome/GlobalShopHome'
import UpcomingLessonsNearYou from '../UpcomingLessonsNearYou/UpcomingLessonsNearYou'

export default {
  name: 'Home',
  components: {
    BecomeAnInstructor,
    FeaturedInstructors,
    GlobalShopHome,
    UpcomingLessonsNearYou
  },
  props: {
    preloadedLessons: {
      type: Array,
      default: () => {
        return []
      },
    },
    canBook: {
      type: Boolean,
      default: false,
    },
  }
}
</script>

<style lang='scss' scoped>
@import './Home.scss';
</style>
