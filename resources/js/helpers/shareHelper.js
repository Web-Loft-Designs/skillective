const shareHelper = {

  buildShareLink(instructorId, lessonId, date) {
    return `${ window.location.protocol }//${ window.location.hostname }/profile/${ instructorId }?lessonId=${ lessonId }&date=${date}`;
  },
  
};

export default shareHelper;