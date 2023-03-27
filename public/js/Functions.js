function setAlert(responseText) {
    var alert = document.getElementById("alert");
    
    alert.innerHTML=responseText;
    alert.classList.remove('inactive');

    window.setTimeout(function(){
        alert.classList.add('inactive');
    }, 5000);
}

// The code below is to open the instruction pop-up if it is required.
var modal = document.getElementById('showInstruction');
if (modal != null) {
    var QRModal = new bootstrap.Modal(modal);
    QRModal.show();
}