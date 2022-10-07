<template>
    <div id="profile-form-container">
        <form method="post" @submit.prevent="onSubmit">

            <p class="login-box-msg">Notifications</p>
            <p class="text" v-if="description!=''" v-html="description"></p>


            <div class="d-flex flex-wrap">
                <div class="form-group checkbox-wrapper has-feedback" :class="{ 'has-error' : errors.notification_methods }">
                    <div class="field mb-4" v-for="(notificationMethodName, notificationMethodKey) in availableNotificationMethods">
                        <label :for="'method-'+notificationMethodKey">
                            <input v-model="fields.notification_methods" type="checkbox" :id="'method-'+notificationMethodKey" :value="notificationMethodKey">
                            <span class="checkmark"></span>
                            {{ notificationMethodName }}
                        </label>
                    </div>
                    <span class="help-block" v-if="errors.notification_methods">
                    <strong>{{ errors.notification_methods[0] }}</strong>
                </span>
                </div>

                <div v-if="errorText" class="form-group has-error">{{ errorText }}</div>

                <div class="form-group"><button type="submit" class="btn btn-primary">Save</button></div>
            </div>


        </form>

    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';

	export default {
		mixins : [siteAPI],
		props: {
			availableNotificationMethods : null,
			userNotificationMethods : null,
			userId : null,
			description : ''
        },
		data() {
			return {
				fields: {
					notification_methods : [],
                }
			}
		},
		methods: {
			onSubmit() {
				var submitUrl = '/api/user/notification_methods';
				if (this.userId!=null)
					submitUrl += ('/' + this.userId);
				this.apiPut(submitUrl, this.fields);
			}
		},
        created : function() {
			this.fields.notification_methods = _.intersection(_.keys(this.availableNotificationMethods), this.userNotificationMethods);
        }
	}
</script>