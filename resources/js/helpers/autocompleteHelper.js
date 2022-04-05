const autocompleteHelper = {

  getAutocomplete(words, text) {
    if (words.length) {
      for (let i = 0; i < words.length; i++) {
        const index = words[i].toLowerCase().indexOf(text.toLowerCase());
        if (index != -1) {
          return words[i].substring(index + text.length, words[i].length);
        }
      }
    }
    return "";
  },
  
  highlightAutocomplete(str, text) {
    const index = str.toLowerCase().indexOf(text.toLowerCase());
    if (index != -1) {
      return str.substring(0, index) + "<span style='color: #222'>" + 
        str.substring(index, index + text.length) + "</span>" + str.substring(index + text.length, str.length);
    }
    return str;
  },

};

export default autocompleteHelper;