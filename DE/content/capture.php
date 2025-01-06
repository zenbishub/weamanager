<script src="../vendor/jquery/jquery.min.js"></script>
<video id="camvideo" style="max-height:85vh;" autoplay></video>
<canvas id="camcanvas" width="960px" height="740px" style="display:none"></canvas>
<script>
let video = document.querySelector("#camvideo");
let canvas = document.querySelector("#camcanvas");
async function start() {
    let stream = await navigator.mediaDevices.getUserMedia({
        video: true,
        audio: false
    });
    video.srcObject = stream;
}
start();
setTimeout(function() {
    setInterval(function() {
        canvas.getContext('2d').drawImage(video, 0, 0, 960, 740);
        let image_data_url = canvas.toDataURL('image/jpeg');
        $.post("../class/ajax_cam.php", {
            "capture": image_data_url
        });
    }, 200);

}, 3000);
</script>