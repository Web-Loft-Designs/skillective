let skillectiveHelperMixin =  {

	data : function(){
		return {
		}
	},
	methods : {
		getTimeOptions(){
			var _timeOptions = [];
			var interval_minutes = 30;
			var start_time = 0;
			var ap = ['AM', 'PM']; // AM-PM

			//loop to increment the time and push results in array
			for (var i=0;start_time<24*60; i++) {
				var hh = Math.floor(start_time/60); // getting hours of day in 0-24 format
				var mm = start_time%60; // getting minutes of the hour in 0-55 format
				var hhTitle = ("0" + (hh % 12)).slice(-2);
				var pmAmTitle = ap[Math.floor(hh/12)];
				if (hhTitle=='00' && pmAmTitle==ap[1])
					hhTitle = 12;

				_timeOptions[i] = {
					value : hh + ':' + ("0" + mm).slice(-2) + ':00',
					title : hhTitle + ':' + ("0" + mm).slice(-2) + pmAmTitle
				}; // pushing data in array in [00:00 - 12:00 AM/PM format]
				start_time = start_time + interval_minutes;
			}
			return _timeOptions;
		},
		getUrlParameter(name) {
			name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
			var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
			var results = regex.exec(location.search);
			return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
		},
		updateUrlQueryParams(paramsObj) {
			if (history.pushState) {

				let searchParams = new URLSearchParams(window.location.search);

				searchParams.forEach(function(value, key) {
					searchParams.delete(key);
				});


				for (var prop in paramsObj){
					if (paramsObj.hasOwnProperty(prop))
						searchParams.set(prop, paramsObj[prop]);
				}
				let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString();
				window.history.pushState({path: newurl}, '', newurl);
			}
		},
		initializeLocationField(input, _types) {

			if (_types == undefined){
				_types = ['(cities)'] // // '(regions)',
			}
			var options = {
				types: _types,
				componentRestrictions: {country: "us"}
			};
			var autocomplete = new google.maps.places.Autocomplete(input, options);

			return autocomplete;
		},
		getPlaceAddressComponents(placeObj){
			var components_by_type = {};
			for (var i = 0; i < placeObj.address_components.length; i++) {
				var c = placeObj.address_components[i];
				components_by_type[c.types[0]] = c;
			}
			// administrative_area_level_1.short_name = state ; locality.long_name = city ; country.short_name = US
		},
		downloadFile(response, filename, mime, ext) {
			// It is necessary to create a new blob object with mime-type explicitly set
			// otherwise only Chrome works like it should
			var newBlob = new Blob([response.body], {type: mime}); // 'application/pdf'

			// IE doesn't allow using a blob object directly as link href
			// instead it is necessary to use msSaveOrOpenBlob
			if (window.navigator && window.navigator.msSaveOrOpenBlob) {
				window.navigator.msSaveOrOpenBlob(newBlob);
				return;
			}

			// For other browsers:
			// Create a link pointing to the ObjectURL containing the blob.
			const data = window.URL.createObjectURL(newBlob);
			var link = document.createElement('a')
			link.href = data
			link.download = filename + ext;//'.pdf'
			link.click()
			setTimeout(function () {
				// For Firefox it is necessary to delay revoking the ObjectURL
				window.URL.revokeObjectURL(data);
			}, 100);
		},
		getLessonTypeName(lesson_type){
			if (window.lessonTypes!=undefined){
                let lessonTypes = window.lessonTypes;
                if (lessonTypes[lesson_type]!=undefined)
                	return window.lessonTypes[lesson_type];
			}

			return _.startCase(_.toLower(lesson_type.replace(/_/, ' ')));
		}
	}
}

export default skillectiveHelperMixin