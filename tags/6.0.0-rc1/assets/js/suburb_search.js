function bkfSuburbSearchShowResult(str) {
	var noResults = document.getElementById("bkflivesearch").dataset.noresults;
	var header = document.getElementById("bkflivesearch").dataset.header;
	if (str.length==0) {
		document.getElementById("bkflivesearch").innerHTML="";
		document.getElementById("bkflivesearch").style.display="none";
		return;
	}
	var xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function() {
		if (this.readyState==4 && this.status==200) {
			document.getElementById("bkflivesearch").innerHTML=this.responseText;
			document.getElementById("bkflivesearch").style.display="block";
		}
	}
	xmlhttp.open("GET", thisAjaxUrl+str+'&noresults='+noResults+'&header='+header, true);
	xmlhttp.send();
}
function bkfBreakdanceSuburbSearchShowResult(str) {
	var noResults = document.getElementById("bkfbdlivesearch").dataset.noresults;
	var header = document.getElementById("bkfbdlivesearch").dataset.header;
	if (str.length==0) {
		document.getElementById("bkfbdlivesearch").innerHTML="";
		document.getElementById("bkfbdlivesearch").style.display="none";
		return;
	}
	var xmlhttp=new XMLHttpRequest();
	xmlhttp.onreadystatechange=function() {
		if (this.readyState==4 && this.status==200) {
			document.getElementById("bkfbdlivesearch").innerHTML=this.responseText;
			document.getElementById("bkfbdlivesearch").style.display="block";
		}
	}
	xmlhttp.open("GET", thisAjaxUrl+str+'&noresults='+noResults+'&header='+header, true);
	xmlhttp.send();
}