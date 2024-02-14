jQuery('#bkfCopyBtn').on("click", function($){
	var copyText = document.getElementById("bkfCopy");
	copyText.select();
	copyText.setSelectionRange(0, 99999);
	navigator.clipboard.writeText(copyText.value);
	
	var tooltip = document.getElementById("bkfpTooltip");
	tooltip.innerHTML = copied;
});

jQuery('#bkfCopyBtn').on("mouseout", function(){
	var tooltip = document.getElementById("bkfpTooltip");
	tooltip.innerHTML = toCopy;
	});