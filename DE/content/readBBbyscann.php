<?php 
extract($_REQUEST);
?>


<div id="qr-reader" style="width:100%"></div>
<div id="qr-reader-results"></div>
<div id="kode"></div>

<script src="js/html5-qrcode.min.js?t=<?=time()?>"></script>

<script>
var html5QrCode = new Html5Qrcode("qr-reader");


var scannBB = $("#scannBB");
var scannLT = $("#scannLT");
var scannrequire = "<?=$scannrequire?>";


var scannok = new Audio('assets/sound/scanok.mp3');
var scannnotok = new Audio('assets/sound/scannnotok.mp3');

var qrCodeSuccessCallback = (decodedText, decodedResult) => {
    /* handle success */
    //alert(`Scan result: ${decodedText}`, decodedResult);
    //let hostname = window.location.hostname;
    var splitDecodedText = decodedText.substr(0, 2);

    //var data = $("#getBB");
    //data.val(decodedText);
    //$("#barcodeModal").modal("hide");

    if (splitDecodedText != "BB" && scannrequire == "BB") {
        scannnotok.play();
        alert("BB-NUmmer nicht korrekt");
        return;
    }
    if (splitDecodedText != "60" && scannrequire == "LT" &&
        splitDecodedText != "48" && scannrequire == "LT" &&
        splitDecodedText != "20" && scannrequire == "LT"
    ) {
        scannnotok.play();
        alert("LT-NUmmer nicht korrekt");
        return;
    }

    if (scannrequire == "BB") {
        scannok.play();
        scannBB.css({
            "background": "green",
            "color": "#fff"
        }).val(decodedText);

    }
    if (scannrequire == "LT") {
        scannok.play();
        scannLT.css({
            "background": "green",
            "color": "#fff"
        }).val(decodedText);
    }

    html5QrCode.stop();
    $("#overlay").remove();
    return;
};
var config = {
    fps: 5,
    qrbox: 500
};
// html5QrCode.start({
//     facingMode: {
//         exact: "user"
//     }
// }, config, qrCodeSuccessCallback);
html5QrCode.start({
    facingMode: {
        exact: "environment"
    }
}, config, qrCodeSuccessCallback);
</script>