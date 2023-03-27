var currentTab = 0;
showTab(currentTab);

function showTab(n) {
    var x = document.getElementsByClassName("tab");
    x[n].style.display = "block";

    if (n == 0) {
        document.getElementById("prevBtn").style.display = "none";
    } else {
        document.getElementById("prevBtn").style.display = "inline";
    }

    if (n == (x.length - 1)) {
        document.getElementById("nextBtn").innerHTML = "Naar betalen";
    } else {
        document.getElementById("nextBtn").innerHTML = "Verder";
    }
}

function nextPrev(n) {
    var x = document.getElementsByClassName("tab");
    if (n == 1 && !validateForm()) return false;

    if (currentTab == x.length - 1 && n == 1) {
        document.getElementById("checkout_form").submit();
        return false;
    } else {
        x[currentTab].style.display = "none";
        currentTab = currentTab + n;
    }

    showTab(currentTab);
}

function validateForm() {
    var x, y, i, valid = true;
    x = document.getElementsByClassName("tab");
    y = x[currentTab].getElementsByTagName("input");

    for (i = 0; i < y.length; i++) {
        y[i].classList.remove("is-invalid");
        y[i].classList.remove("is-valid");

        if (y[i].value == "") {
            y[i].className += " is-invalid";
            valid = false;
        } else {
            if (currentTab == 1 && !checkTable(y[i].value)) {
                y[i].className += " is-invalid";
                valid = false;
                document.getElementById("invalid-feedback").innerHTML = '<p class="mb-3">Dit tafelnummer is bij ons onbekend.</p>';
            } else {
                y[i].className += " is-valid";
            }
        }
    }

    return valid;
}

function checkTable(table) {
    var success = false;

    function reqListener () {
        var result = this.responseText.trim();
        if (result == '1') success = true;
    }
    
    var oReq = new XMLHttpRequest();
    oReq.addEventListener("load", reqListener);
    oReq.open("GET", URL_ROOT + "/Checkout/tableExists/" + table, false);
    oReq.send();

    return success;
}