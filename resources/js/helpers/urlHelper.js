const dateHelper = {

  buildRequestParamsStr(path, paramsObj) {
    let searchParams = new URLSearchParams();

    for (let prop in paramsObj) {
      if (paramsObj.hasOwnProperty(prop)) {
        if (!paramsObj[prop]) {
          searchParams.delete(prop);
        } else {
          searchParams.set(prop, paramsObj[prop]);
        }
      }
    }

    const paramsStr = searchParams.toString();
    if (paramsStr.length) {
      path += "?" + paramsStr;
    }
    return path;
  },

  updateQueryParams(paramsObj, clearCurrentParams = true, goToUrl = false, path = null) {
    if (window.history.pushState) {
      let searchParams = new URLSearchParams(window.location.search);

      if (clearCurrentParams) {
        searchParams.forEach(function(value, key) {
          searchParams.delete(key.toString());
        });
      }

      for (let prop in paramsObj) {
        if (paramsObj.hasOwnProperty(prop)) {
          if (!paramsObj[prop]) {
            searchParams.delete(prop);
          } else {
            searchParams.set(prop, paramsObj[prop]);
          }
        }
      }

      const paramsStr = searchParams.toString();
      let newUrl;
      if (path) {
        newUrl = window.location.protocol + "//" + window.location.hostname + path;
      } else {
        newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname;
      }
      
      if (paramsStr.length) {
        newUrl += "?" + paramsStr;
      }

      if (goToUrl) {
        window.location.href = newUrl;
      } else {
        window.history.pushState({ path: newUrl }, "", newUrl);
      }
    }
  },

  parseQueryParams() {
    const urlSearchParams = new URLSearchParams(window.location.search);
    return Object.fromEntries(urlSearchParams.entries());
  },

  fileNameFromUrl(url, removeNulls = false) {
    if (Array.isArray(url)) {
      url = url.map((value) => {
        if (value) {
          const parts = value.split('/');
          return parts[parts.length - 1];
        }
      });
      if (removeNulls) {
        url = url.filter((item) => {
            return item;
        });
      }
      return url;
    } else {
      if (url) {
        const parts = url.split('/');
        return parts[parts.length - 1];
      }
    }
    return null;
  },

  fileExtFromUrl(url) {
    if (url) {
      const parts = url.split('.');
      return parts[parts.length - 1].toUpperCase();
    }
    return null;
  },
  
};

export default dateHelper;