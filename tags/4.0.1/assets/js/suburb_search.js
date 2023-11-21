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
	xmlhttp.open("GET",ajaxUrl+str+'&noresults='+noResults+'&header='+header,true);
	xmlhttp.send();
}