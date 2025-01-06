    <nav class="navbar navbar-expand-sm navbar-light bg-light pb-0">
        <div class="container-fluid">
            <div class="collapse navbar-collapse" id="navbarID">
                <div class="navbar-nav">
                    <h3>Ladungsträgerinhalt</h3>
                </div>
            </div>
        </div>
    </nav>
    <div class="row pt-3">
        <div class="col-md-7 mb-2">
            <div id="qr-reader" style="width:100%"></div>
            <div id="qr-reader-results"></div>
            <div id="kode"></div>
        </div>
        <div class="col-md-5">
            <form action="../../NU/lack/class/action" id="form-find_boxcontent">
                <div class="row">
                    <div class="col-9">
                        <input type="text" class="form-control" name="boxnummer" placeholder="Boxnummer eingeben"
                            required>
                    </div>
                    <div class="col-3">
                        <input type="hidden" name="find_boxcontent" value="1">
                        <button type="submit" class="btn btn-primary">ok</button>
                    </div>
                </div>
            </form>
            <hr>
            <div class="card p-0">
                <div class="card-header">Positionen
                    <span class="float-end btn btn-light p-1" id="reset-table"><i class="ti ti-reload"></i></span>
                </div>
                <div class="card-body p-2 overflow-auto" id="boxnummer_content">
                </div>
            </div>
        </div>

    </div>
    <script src="js/html5-qrcode.min.js?t=<?=time()?>"></script>
    <script>
var screenWith = $(window).width();
var camera = "environment";
var html5QrCode = new Html5Qrcode("qr-reader");
var qrCodeSuccessCallback = (decodedText, decodedResult) => {
    /* handle success */
    //alert(`Scan result: ${decodedText}`, decodedResult);
    // return;
    var splitDecodedText = decodedText.split("-");
    var redirect = "../../NU/lack/class/action?find_boxcontent=1&boxnummer=" + decodedText;
    if (!splitDecodedText[1]) {
        alert("Invalid Code!");
        return;
    }
    $.post("../../NU/lack/class/action", {
        find_boxcontent: 1,
        boxnummer: decodedText
    }, function(response) {

        var boxnummer_content = $("#boxnummer_content");
        boxnummer_content.html(response);
        $("#reset-table").click(function() {
            boxnummer_content.html("");
        });

    });
    html5QrCode.clear();
};
var config;

camera = "environment";
//camera = "user";
config = {
    fps: 15,
    qrbox: {
        "width": 250,
        "height": 250
    }
};
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