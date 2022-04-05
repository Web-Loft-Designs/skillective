const dateHelper = {

  monthsList() {
    return ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
  },

  monthNameByNumber(num) {
    return this.monthsList()[num];
  },

  threeLetMonthNameByNumber(num) {
    return this.monthsList()[num].substring(0, 3);
  },

  formatDate(startStr, endStr = null) {
    const start = new Date(startStr);
    if (endStr) {
      const end = new Date(endStr);
      return this.threeLetMonthNameByNumber(start.getMonth()) + " " + start.getDate() + ", " + this.formatTime(start.getHours(), start.getMinutes()) +
        " - " + this.threeLetMonthNameByNumber(end.getMonth()) + " " + end.getDate() + ", " + this.formatTime(end.getHours(), end.getMinutes());
    } else {
      return this.threeLetMonthNameByNumber(start.getMonth()) + " " + start.getDate() + ", " + this.formatTime(start.getHours(), start.getMinutes());
    }
  },

  formatDateWithoutTime(startStr, endStr = null) {
    const start = new Date(startStr);
    if (endStr) {
      const end = new Date(endStr);
      return this.threeLetMonthNameByNumber(start.getMonth()) + " " + start.getDate()
        +  " - " + 
        dateHelper.threeLetMonthNameByNumber(end.getMonth()) + " " + end.getDate();
    } else {
      return this.threeLetMonthNameByNumber(start.getMonth()) + " " + start.getDate();
    }
  },

  formatTime(hours, minutes) {
    var ampm = hours >= 12 ? ' PM' : ' AM';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var strTime = hours + ':' + minutes + ampm;
    return strTime;
  },

  dateToFilter(date) {
    return date.getFullYear() + "/" + (date.getMonth() + 1) + "/" + date.getDate();
  },

  filterToDate(str) {
    const arr = str.split("/");
    let date = new Date();
    date.setFullYear(arr[0]);
    date.setMonth(Number(arr[1]) - 1);
    date.setDate(arr[2]);
    return date;
  },

  flexibleMonthsToFilter(months) {
    let flexMonths = "";
    months.map((month, index) => {
      flexMonths += (month.number + 1);
      if (index < months.length - 1) {
         flexMonths += ",";
      }
    });
    return flexMonths;
  },

  filterToFlexibleMonths(str) {
    let flexMonths = [];
    let arr = str.split(",");
    arr.map((item, index) => {
      arr[index] = { active: true, number: (item - 1) };
    });
    flexMonths = arr;
    return flexMonths;
  },

  formatFlexibleDate(flexibleDays, flexibleMonths) {
    let res = flexibleDays + ", ";
    flexibleMonths.forEach((value, index) => {
      if (value.active) {
        res += this.monthNameByNumber(value.number);
        if (index < flexibleMonths.length - 1) {
          res += ", ";
        }
      }
    });

    return res;
  },

  filterToTimeObj(str) {
    let date = {};
    const arr = str.split(":");
    date.h = arr[0];
    date.mm = arr[1];
    date.a = "am";
    if (date.h == "00") {
      date.h = "12";
    } else if (Number(date.h) > 12) {
      date.h = (Number(date.h) - 12).toString();
      date.a = "pm";
    }
    return date;
  },

  timeObjToFilter(obj) {
    if (obj.h == "12" && obj.a == "am") {
      obj.h = "00";
    } else if (obj.a == "pm") {
      obj.h = (Number(obj.h) + 12).toString();
    }
    return obj.h + ":" + obj.mm;
  },
  
};

export default dateHelper;