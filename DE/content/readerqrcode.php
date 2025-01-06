<div id="qr-reader" style="width:100%"></div>
<div id="qr-reader-results"></div>
<div id="kode"></div>

<script src="js/html5-qrcode.min.js?t=<?=time()?>"></script>
<script>
var screenWith = $(window).width();
var camera = "environment";
var html5QrCode = new Html5Qrcode("qr-reader");
var qrCodeSuccessCallback = (decodedText, decodedResult) => {
    /* handle success */
    //alert(`Scan result: ${decodedText}`, decodedResult);
    let hostname = window.location.hostname;
    var splitDecodedText = decodedText.substr(0, 3);
    var wartenummer = decodedText;
    var redirect = "https://" + hostname + "/weamanager/de/class/publicAjax?requestNummer=" + wartenummer;
    if (splitDecodedText != "req" && splitDecodedText != "sav") {
        alert("Invalid Code!");
        return;
    }
    if (splitDecodedText == "req") {
        redirect = "https://" + hostname + "/weamanager/de/class/action?Wartenummer=" + wartenummer;
    }
    document.getElementById('kode').value = decodedText;
    // alert(redirect);
    location.href = redirect;
    html5QrCode.clear();
};
var config;

if (screenWith < 500) {
    camera = "environment";
    config = {
        fps: 15,
        qrbox: {
            "width": 200,
            "height": 200
        }
    };
}

if (screenWith > 500) {
    camera = "user";
    config = {
        fps: 15,
        qrbox: {
            "width": 250,
            "height": 250
        }
    };
}

// Select front camera or fail with `OverconstrainedError`.
html5QrCode.start({
    facingMode: {
        exact: camera
    }
}, config, qrCodeSuccessCallback);
$(".btn-close").click(function() {
    html5QrCode.stop();
    location.href = location.href;
});
</script>