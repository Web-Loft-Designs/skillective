<template>
    <div>
        <form class="d-flex send-invate-wrapper" method="post" @submit.prevent="onSubmit" v-if="(maxInvitesEnabled-countSentInvites)>0">
            <input type="text" class="form-control" required v-model="fields.invited_contact" :placeholder="inputPlaceholder">
            <button class="send-invate" type="submit">Send invite</button>
        </form>
        <span class="help-block mt-2 has-success" v-if="errors.invited_contact">
                <strong>{{ errors.invited_contact[0] }}</strong>
        </span>

        <div v-if="errorText" class="has-error mt-2">{{ errorText }}</div>
        <div v-if="formSubmitted" class="mt-2 has-success">{{ successText }}</div>

        <div v-if="(maxInvitesEnabled-countSentInvites)<=0">All {{ maxInvitesEnabled }} available invitations were already sent</div>

    </div>
</template>

<script>
	import siteAPI from '../mixins/siteAPI.js';
	import $ from 'jquery'

	export default {
		mixins : [siteAPI],
        props : {
			maxInvitesEnabled : Number,
			countInvitationsSent : Number,
            alternativeInviteInputPlaceholder : String,
            clearForm: Boolean,
        },
		data() {
			return {
				formSubmitted: false,
                inputPlaceholder : 'Client’s Email address or Mobile phone',
				fields: {
					invited_contact : ''
                },
				countSentInvites : Number
			}
		},
		methods: {
			onSubmit() {
				this.apiPost('/api/invite-student', this.fields);
			},
			componentHandlePostResponse(responseData) {
				this.updateCountNotifications();
				this.clearSubmittedForm();
				this.formSubmitted = true;
				setTimeout(() => {
					this.formSubmitted = false;
				},1000)
			},
			updateCountNotifications(){
				this.countSentInvites++;
				var _countSentContainer = $('.count-sent-student-invitations:first');
				if (_countSentContainer.length==1){
					var _countSent = parseInt(_countSentContainer.text());
					$('.count-sent-student-invitations').text( _countSent + 1 );
				}

				var _countAvailableContainer = $('.count-available-student-invitations:first')
                if (_countAvailableContainer.length==1){
					var _countAvailable = parseInt(_countAvailableContainer.text());
					$('count-available-student-invitations').text( _countAvailable - 1 );
                }
            }
		},
        watch: {
            clearForm: function() {
                if(this.clearForm) {
                    this.successText = null;
                    this.errorText = null;
                    this.fields.invited_contact = '';
                }
            }
        },
		created : function(){
			this.countSentInvites = this.countInvitationsSent;
			if (this.alternativeInviteInputPlaceholder!=undefined && this.alternativeInviteInputPlaceholder!='')
			    this.inputPlaceholder = this.alternativeInviteInputPlaceholder
		}
	}
</script>