function bkfCopy() {
  var copyText = document.getElementById("bkfCopy");
  copyText.select();
  copyText.setSelectionRange(0, 99999);
  navigator.clipboard.writeText(copyText.value);
  
  var tooltip = document.getElementById("bkfpTooltip");
  tooltip.innerHTML = "Copied to clipboard!";
}

function bkfpCopyOut() {
  var tooltip = document.getElementById("bkfpTooltip");
  tooltip.innerHTML = "Copy to clipboard";
}