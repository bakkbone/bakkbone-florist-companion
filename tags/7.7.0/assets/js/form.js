function bkfValidateFormClosedRange() {
  var date1 = document.forms["add-closed-range"]["date1"].value;
  var date1val = Date.parse(date1);
  var date2 = document.forms["add-closed-range"]["date2"].value;
  var date2val = Date.parse(date2);
  
  if (date1 == "" || date2 == "") {
    alert(formlang["emptyField"]);
    return false;
  } else if (date1val >= date2val) {
    alert(formlang["compareDates"]);
    return false;
  }
}

function bkfValidateFormFullRange() {
  var date1 = document.forms["add-full-range"]["date1"].value;
  var date1val = Date.parse(date1);
  var date2 = document.forms["add-full-range"]["date2"].value;
  var date2val = Date.parse(date2);
  
  if (date1 == "" || date2 == "") {
    alert(formlang["emptyField"]);
    return false;
  } else if (date1val >= date2val) {
    alert(formlang["compareDates"]);
    return false;
  }
}

function bkfValidateFormCatBlockRange() {
  var date1 = document.forms["addrangeform"]["date1"].value;
  var date1val = Date.parse(date1);
  var date2 = document.forms["addrangeform"]["date2"].value;
  var date2val = Date.parse(date2);
  
  if (date1 == "" || date2 == "") {
    alert(formlang["emptyField"]);
    return false;
  } else if (date1val >= date2val) {
    alert(formlang["compareDates"]);
    return false;
  }
}