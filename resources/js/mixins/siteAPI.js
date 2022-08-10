let siteAPIMixin =  {
	props : {
		usStates : null,
		siteGenres: null
	},
	data : function(){
		return {
			listItems : [],
			itemToDelete : [],
			fields : {},
			errors: {},
			errorText: null,
			successText : null,
			loader : null,
			autoHideLoader : true
		}
	},
	methods : {
		apiGetUSStates : function(){
			axios.get('/api/us-states', {})
			.then(response => {
				this.usStates = response.data;
			}).catch(error => {
				this.usStates = [];
			});
		},
		apiGetGenres : function(){
			axios.get('/api/genres', {})
			.then(response => {
				this.siteGenres = response.data;
			}).catch(error => {
				this.siteGenres = [];
			});
		},
		getUerMedia(galleryUserId) {
			axios.get('/api/user/'+ galleryUserId +'/media', {})
			.then(response => {
				this.listItems = response.data;
			}).catch(error => {
				this.listItems = [];
			});
		},
		apiGet : function(action, data){
			this.apiPreSend();

			axios.get(action, data)
				.then(response => {
					this.apiHandleResponse(response);
					if (this.hasOwnProperty('componentHandleGetResponse')){
						this.componentHandleGetResponse(response.data, action)
					}
				})
				.catch(error => this.apiHandleError(error))
				// .finally(() => this.hideLoader());
		},
		apiPost : function(action, data){
			this.apiPreSend();
			axios.post(action, data)
				.then(response => {
					this.apiHandleResponse(response);
					if (this.hasOwnProperty('componentHandlePostResponse')){
						this.componentHandlePostResponse(response.data)
					}
				})
				.catch(error => this.apiHandleError(error))
		},
		apiPut : function(action, data){
			this.apiPreSend();
			axios.put(action, data)
				.then(response => {
					this.apiHandleResponse(response);
					if (this.hasOwnProperty('componentHandlePutResponse')){
						this.componentHandlePutResponse(response.data)
					}
				})
				.catch(error => this.apiHandleError(error))
				// .finally(() => this.hideLoader());
		},
		apiDelete : function(action){
			this.apiPreSend();

			axios.delete(action)
				.then(response => {
					this.apiHandleResponse(response);
					if (this.hasOwnProperty('componentHandleDeleteResponse')){
						this.componentHandleDeleteResponse(response.data)
					}
				})
				.catch(error => this.apiHandleError(error))
				// .finally(() => this.hideLoader());
		},
		apiPreSend : function(){
			this.errorText = null;
			this.errors = {};
			if (this.loader==null){
				this.loader = this.$loading.show({
					zIndex: 9999999,
				});
			}
		},
		apiHandleError : function(error){
			this.autoHideLoader = true;
			this.hideLoader();
			if (error.response!=undefined && error.response.status === 422) {
				this.errors    = error.response.data.errors || {};
				this.errorText = error.response.data.message;
			}else if (error.response!=undefined && error.response.status === 419) {
				this.errorText = error.response.data.message || 'Unable to process your request. Reload the page please and try again';
			}else if(error.response!=undefined){
				this.errorText = error.response.data.message || 'Unable to process your request.';

			}else{
				console.log(error);
				this.errorText = 'Unable to process your request';
			}
		},
		apiHandleResponse : function(response){
			this.hideLoader();
			if ( response.data != undefined && response.data.message != undefined )
				this.successText = response.data.message;
		},
		clearSubmittedForm : function(){
			if (this.fields != undefined){
				for (var _prop in this.fields){
					if (this.fields.hasOwnProperty(_prop)){
						this.fields[_prop] = (_prop==='id') ? null : '';
					}
				}
			}
			this.errors = {};
			this.errorText =  null;
		},
		hideLoader : function(){
			if (this.autoHideLoader && this.loader!=null){
				this.loader.hide();
				this.loader = null;
			}
		}
	}
}

export default siteAPIMixin