<?php extract($_REQUEST);
require_once '../class/classPublic.php';
$o = new classPublic();
$lang = $o->selectRunLeafLanguage("../");
?>
<div class="card">
    <div class="card-body p-0 text-center">
        <h5 class="pt-2 pb-2"><?=$lang['string26']?></h5>
        <video id="video" autoplay></video>
        <div id="dataurl-container" class="container p-0">
            <canvas id="canvas"></canvas>
        </div>
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-12 p-0 text-center">
                <button class="btn-lg btn-rounded text-white  btn-primary" id="click-photo"><i
                        class="ti-camera"></i></button>
            </div>
        </div>
    </div>
</div>
<div id="hidden"><canvas id="hiddencanvas" width="2500" height="3200"></canvas></div>
<script>
var route = "<?=$route?>";
var rfnum = "<?=$rfnum?>";
var knznummer = "<?=$knznummer?>";
var picture_autor = "<?=$picture_autor?>";
var camera_button = document.querySelector("#start-camera");
var video = document.querySelector("#video");
var click_button = document.querySelector("#click-photo");
var canvas = document.querySelector("#canvas");
var image_checked = document.querySelector("#image-checked");
var dataurl_container = document.querySelector("#dataurl-container");
var imageLoaded = document.querySelector("#image-loaded");
var hidden = document.querySelector("#hidden");
var hiddencanvas = document.querySelector("#hiddencanvas");



dataurl_container.style.display = 'none';
hidden.style.display = 'none';
async function startCamera() {
    var stream = null;
    //environment
    //stream = await navigator.mediaDevices.getUserMedia({ video:{facingMode: 'user'}, audio: false });
    stream = await navigator.mediaDevices.getUserMedia({
        video: {
            facingMode: 'environment'
        },
        audio: false
    });
    video.srcObject = stream;
    video.style.display = 'block';
    $(".close").click(function() {
        stream.getTracks().forEach((track) => {
            if (track.readyState == 'live' && track.kind === 'video') {
                track.stop();
            }
        });

        // $("#take-image-from-document, .btn-image").removeClass("btn-success")
        //     .removeClass("text-white").html(
        //         "<span class='font-large'><i class='ti-camera'></i></span>");


    });
}

click_button.addEventListener('click', function() {
    let W = video.clientWidth;
    let H = video.clientHeight;

    click_button.style.display = "none";
    canvas.style = "width:" + W + "px; height:" + H + "px";
    canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
    hiddencanvas.getContext('2d').drawImage(video, 0, 0, hiddencanvas.width, hiddencanvas.height);
    var lieferschein = document.querySelector("#" + route);
    var image_data_url = canvas.toDataURL('image/jpeg');
    var image_data_send = hiddencanvas.toDataURL('image/jpeg');
    video.style.display = 'none';

    $("#take-image-from-document, .btn-image").removeClass("btn-light").addClass("btn-success").addClass(
        "text-white").text(
        "Foto OK");
    lieferschein.value = image_data_send;
    dataurl_container.style.display = 'block';
    if (picture_autor == "wea" && route == "lieferschein") {
        $.post("class/publicAjax.php", {
            "save_image_wea": picture_autor,
            "rfnum": rfnum,
            "knznummer": knznummer,
            "lieferschein": image_data_send,
        }, function(data) {
            if (data) {
                //alert("Foto OK");
            }
        });
    }
});
startCamera();
</script>