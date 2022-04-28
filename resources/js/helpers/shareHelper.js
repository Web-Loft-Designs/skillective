const shareHelper = {

  buildShareLink(instructorId, lessonId) {
    return `${ window.location.protocol }//${ window.location.hostname }/profile/${ instructorId }?lessonId=${ lessonId }`;
  },
  
};

export default shareHelper;