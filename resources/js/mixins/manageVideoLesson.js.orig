let manageVideoLessonMixin =  {

	data : function(){
		return {

		}
	},
	methods : {
        // startLesson(lessonId){
        //     this.apiPreSend();
        //     axios.post('/api/instructor/virtual-lessons/' + lessonId + '/room/create')
        //         .then(response => {
        //             this.apiHandleResponse(response);
        //             window.location.reload();
        //             // this.joinLesson(lessonId, response.data.instructorName, response.data.genreTitle);
        //         })
        //         .catch(error => this.apiHandleError(error));
        // },
        completeLesson(lessonId){
            this.apiPreSend();
            axios.delete('/api/instructor/virtual-lessons/' + lessonId + '/room/complete')
                .then(response => {
                    this.apiHandleResponse(response);
                    window.location.reload();
                })
                .catch(error => this.apiHandleError(error));
        },
        joinLesson(lessonId, instructorName, genreTitle){
            let videoWindowFeatures = "left=300,top=300,width=700,height=530,menubar=no,toolbar=no,location=no,resizable=yes,scrollbars=yes,status=yes";
            let lessonWindowTitle = instructorName + " Virtual Lesson (" + genreTitle + ")";
            // window.open("/virtual-lesson/" + lessonId + '?lat=qweqwe', lessonWindowTitle, videoWindowFeatures);
            let lessonWindow = window.open("", '_blank', videoWindowFeatures);
            this.apiPreSend();
            axios.post('/api/virtual-lessons/' + lessonId + '/room/join')
                .then(response => {
                    this.apiHandleResponse(response);
                    let lat = response.data.lat;
                    lessonWindow.location.href = "/virtual-lesson/" + lessonId + '?lat='+lat;
                    // window.open("/virtual-lesson/" + lessonId + '?lat='+lat, lessonWindowTitle, videoWindowFeatures);
                })
                .catch(error => {
                    lessonWindow.close();
                    this.apiHandleError(error);
                });
        }
	}
}

export default manageVideoLessonMixin