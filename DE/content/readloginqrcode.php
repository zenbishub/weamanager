    <?php
        $routefolder="stapler";
        extract($_REQUEST);
    ?>
    <div id="qr-reader" style="width:100%"></div>
    <div id="qr-reader-results"></div>
    <div id="kode"></div>
    <script src="js/html5-qrcode.min.js?t=<?=time()?>"></script>
    <script>
const html5QrCode = new Html5Qrcode("qr-reader");
const qrCodeSuccessCallback = (decodedText, decodedResult) => {
    /* handle success */
    //alert(`Scan result: ${decodedText}`, decodedResult);
    let hostname = window.location.hostname;
    var splitDecodedText = decodedText.substr(0, 3);
    var data = decodedText.split(";");
    var redirect = "https://" + hostname + "/weamanager/de/class/action?werknummer=" + data[0] + "&user=" +
        data[1] + "&pass=" + data[2] + "&fixpass=rtool&userlogin=1&app=stapler";
    document.getElementById('kode').value = decodedText;
    //alert(redirect);
    //return
    location.href = redirect;
    html5QrCode.clear();
};

var width = $(window).width();
var camera = "environment";
var config = {
    fps: 10,
    qrbox: {
        "width": 250,
        "height": 250
    }
};
if (width > 1200) {
    camera = "user";
    config = {
        fps: 10,
        qrbox: {
            "width": 250,
            "height": 500
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
    location.href = location.href;
});
    </script>