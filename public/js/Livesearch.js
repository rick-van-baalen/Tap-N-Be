function showResult(str) {
    if (str.length==0) {
        document.getElementById("search-results").innerHTML="";
        return;
    }

    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            document.getElementById("search-results").innerHTML=this.responseText;
        }
    }

    xmlhttp.open("GET", URL_ROOT + "/Menu/search?q="+str,true);
    xmlhttp.send();
}