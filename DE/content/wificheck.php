<link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
<span class="h1" style="color:green; font-size:1.5em" id="wifi-icon"><i class="fas fa-wifi"></i></span>
<span class="ms-3 h1" id="counter"></span>
<script>
var i = 1;
//document.getElementById('counter').innerHTML = i;
setInterval(
    () => {
        //document.getElementById('counter').innerHTML = i;
        if (i > 6) {
            document.getElementById('wifi-icon').style.color = "red";
        }
        i++;
    }, 1000
)
setInterval(() => {
    location.href = location.href;
}, 6000);
</script>