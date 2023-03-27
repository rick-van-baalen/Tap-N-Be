function updateCart() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var order = document.getElementById("order");
            if (order != null) order.innerHTML = this.responseText;
        }
    }

    xmlhttp.open("GET", URL_ROOT + "/Order/updateCart",true);
    xmlhttp.send();
}

function addToOrder(productID) {
    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            document.getElementById("order-count").innerHTML=parseInt(document.getElementById("order-count").innerHTML) + 1;
            updateCart();
            setAlert(this.responseText);
        }
    }

    xmlhttp.open("GET", URL_ROOT + "/Order/addToOrder?productid="+productID,true);
    xmlhttp.send();
}

function removeFromOrder(orderlineID) {
    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            var new_count = parseInt(document.getElementById("order-count").innerHTML) - 1;
            document.getElementById("order-count").innerHTML=new_count;
            updateCart();
            setAlert(this.responseText);
        }
    }

    xmlhttp.open("GET", URL_ROOT + "/Order/removeOrderLine?orderlineid="+orderlineID,true);
    xmlhttp.send();
}

function increaseAmount(productID, orderlineID, URL_ROOT) {
    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            document.getElementById("order-count").innerHTML=parseInt(document.getElementById("order-count").innerHTML) + 1;
            var new_amount = parseInt(document.getElementById("count_" + productID).innerHTML) + 1;
            document.getElementById("count_" + productID).innerHTML=new_amount;
            updateCart();
            setAlert(this.responseText);
        }
    }

    xmlhttp.open("GET", URL_ROOT + "/Order/addToOrder?productid="+productID,true);
    xmlhttp.send();
}

function decreaseAmount(productID, orderlineID, URL_ROOT) {
    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            document.getElementById("order-count").innerHTML=parseInt(document.getElementById("order-count").innerHTML) - 1;
            updateCart();
            setAlert(this.responseText);
        }
    }

    xmlhttp.open("GET", URL_ROOT + "/Order/decreaseAmount?productid="+productID,true);
    xmlhttp.send();
}

function checkAmount(productID, orderlineID, URL_ROOT) {
    var amount = parseInt(document.getElementById("count_" + productID).innerHTML);
    if (amount == 1) {
        var decrease = document.getElementById("decrease_" + productID);
        if (decrease != null) {
            decrease.innerHTML = '<a href="javascript:void(0)" onclick="removeFromOrder(' + orderlineID + ')"><img class="me-2" src="' + URL_ROOT + '/images/icons/trash-fill.svg"></a>';
            decrease.id = "remove_" + productID;
        }
    } else if (amount == 2) {
        var remove = document.getElementById("remove_" + productID);
        if (remove != null) {
            remove.innerHTML = '<a href="javascript:void(0)" onclick="decreaseAmount(' + productID + ', ' + orderlineID + ', \'' + URL_ROOT + '\')"><img class="me-2" src="' + URL_ROOT + '/images/icons/dash-circle-fill.svg"></a>';
            remove.id = "decrease_" + productID;
        }
    }
}

function clearOrder() {
    const xhttp = new XMLHttpRequest();

    xhttp.onload = function() {
        destroyModal();
        
        document.getElementById("order-count").innerHTML = "0";
        document.getElementById("order").innerHTML = "<p class='text-center'>Geen producten in je bestellijstje.</p>";
    }

    xhttp.open("GET", URL_ROOT + "/Order/clearOrder");
    xhttp.send();
}

function destroyModal() {
    var body = document.getElementById("body");
    if (body != null) {
        body.classList.remove('modal-open');
        body.removeAttribute("style");
    }
    
    var modal_backdrop = document.getElementsByClassName('modal-backdrop')[0];
    if (modal_backdrop != null) modal_backdrop.remove();
}

function createQR() {
    var loader = document.getElementById("loader");
    loader.style.display = "block";
    
    var xmlhttp=new XMLHttpRequest();
    xmlhttp.onreadystatechange=function() {
        if (this.readyState==4 && this.status==200) {
            loader.style.display = "none";
            document.getElementById("QR").innerHTML = this.responseText;
            var QRModal = new bootstrap.Modal(document.getElementById('QRModal'));
            QRModal.show();
        }
    }

    xmlhttp.open("GET", URL_ROOT + "/Order/createQR",true);
    xmlhttp.send();
}

function cancelOrder(order_id) {
    const xhttp = new XMLHttpRequest();

    xhttp.onload = function() {
        document.getElementById("cancelOrder").remove();
        destroyModal();

        document.getElementById("order").innerHTML = '<p class="text-center">Order succesvol geannuleerd. Je kunt dit tabblad sluiten.</p>';
    }

    xhttp.open("GET", URL_ROOT + "/Order/cancelOrder?order_id=" + order_id);
    xhttp.send();
}

function completeOrder(order_id) {
    const xhttp = new XMLHttpRequest();

    xhttp.onload = function() {
        document.getElementById("completeOrder").remove();
        destroyModal();

        document.getElementById("order").innerHTML = '<p class="text-center">Order succesvol afgehandeld. Je kunt dit tabblad sluiten.</p>';
    }

    xhttp.open("GET", URL_ROOT + "/Order/completeOrder?order_id=" + order_id);
    xhttp.send();
}

function newOrder() {
    const xhttp = new XMLHttpRequest();

    xhttp.onload = function() {
        window.location.href = URL_ROOT;
    }

    xhttp.open("GET", URL_ROOT + "/Order/createNewOrder");
    xhttp.send();
}