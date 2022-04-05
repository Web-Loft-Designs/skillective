<template>
    <div class="dashboard-gallery dashboard-gallery-page" v-if="this.listItems.length>0">

        <div class="dashboard-gallery-top">
            <h2>Gallery</h2>
        </div>

        <div v-if="instagramMediaQueue">Instagram media will be loaded to gallery soon</div>

        <div id="profile-gallery-container">
            <div class="row gallery-wrapper">
                <div class="col-lg-4 col-md-3 col-sm-6 col-12 gallery-wrapper-item" :class="{'has-video': isVideo(media.url)}" :key="key" v-for="(media, key) in listItems" >
                <span v-if="isVideo(media.url)">
                    <a :href="media.url" :id="'media-'+media.id" class="video-link g-item">
                        <img :src="media.thumb_url"/>
                        <span class="play-icon"></span>
                    </a>
                    <span class="share">
                            <img src="/images/share-icon.png" alt="">
                            <span class="share-tooltip">
                                <span>Share on:</span>
                                <a href="#"><img src="/images/insta.png" alt=""></a>
                                <a href="#"><img src="/images/tw.png" alt=""></a>
                                <a data-sharer="facebook" data-hashtag="hashtag" :data-url="siteUrl+'/profile/'+media.model_id+'#media-'+media.id"><img style="margin-right: 0" src="/images/fb.png" alt=""></a>
                            </span>
                    </span>
                    <!--<span class="info" v-if="media.collection_name=='instagram'">-->
                        <!--<span><img src="/images/heart-cion.png" alt=""> {{ media.count_likes }}</span>-->
                        <!--<span><img src="/images/comment-icon.png" alt=""> {{ media.count_comments }}</span>-->
                    <!--</span>-->
                </span>
                    <span v-else>
                    <a :href="media.url" :id="'media-'+media.id" class="image g-item">
                        <img :src="media.url"/>
                    </a>
                    <span class="share">
                            <img src="/images/share-icon.png" alt="">
                            <span class="share-tooltip">
                                <span>Share on:</span>
                                 <a href="#"><img src="/images/insta.png" alt=""></a>
                                <a data-sharer="twitter" :data-url="siteUrl+'/profile/'+media.model_id+'#media-'+media.id"><img src="/images/tw.png" alt=""></a>
                                <a data-sharer="facebook" :data-url="siteUrl+'/profile/'+media.model_id+'#media-'+media.id"><img style="margin-right: 0" src="/images/fb.png" alt=""></a>
                            </span>
                    </span>
                    <!--<span class="info" v-if="media.collection_name=='instagram'">-->
                        <!--<span><img src="/images/heart-cion.png" alt=""> {{ media.count_likes }}</span>-->
                        <!--<span><img src="/images/comment-icon.png" alt=""> {{ media.count_comments }}</span>-->
                    <!--</span>-->
                </span>
                </div>
            </div>
        </div>
        <div>
            <a v-if="this.userGalleryMedia.length>0" @click="showMore()" class="btn btn-block btn-secondary">Show more</a>

        </div>
    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';

	export default {
		mixins : [siteAPI],
		props : {
			userMedia : Array,
			instagramMediaQueue: Boolean
		},
		data() {
			return {
				portion : 6,
				userGalleryMedia : [],
                siteUrl: ''
			}
		},
		methods: {
			showMore(){
				var counter = 0;
				for (var l in this.userGalleryMedia){
					counter++;
					this.listItems.push( _.cloneDeep(this.userGalleryMedia[l]) );
					if (counter==this.portion)
						break;
				}

				var countToRemove = this.userGalleryMedia.length>=this.portion ? this.portion : this.userGalleryMedia.length;
				this.userGalleryMedia.splice(this.userGalleryMedia.indexOf(this.userGalleryMedia[0]), countToRemove);
			},
            isVideo(element) {
                if(element.split('.').pop() === 'mp4' || element.split('.').pop() === 'mov') {
                    return true;
                } else {
                    return false;
                }
            },
		},
		created: function(){
			this.userGalleryMedia = this.userMedia;
			this.showMore();
            setTimeout(function () {
				if(window.location.hash!=undefined && window.location.hash.length>0) {
                    var hash = window.location.hash.substring(1);
                    var el = document.getElementById(hash);
                    if(el) {
                        el.click();
                    } else {
//                        alert('this media not exist');
                    }
                }
            },1000)
			this.siteUrl = window.location.protocol+'//'+window.location.host;
		}
	}
</script>