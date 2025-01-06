<script src="vendor/jquery/jquery.js"></script>
<script src="vendor/jquery/jquery.qrcode.min.js"></script>
<script>
function showQRcode(output, qrtext) {
    output.qrcode({
        width: 100,
        height: 100,
        text: qrtext
    });
}

function toPrinter(div) {
    var content = document.getElementById(div).innerHTML;
    var body = document.querySelector("body");
    body.removeAttribute("class");
    body.style = "background:#fff";
    body.innerHTML = "<div style='width:500px;margin:50px auto'>" + content + "</div>";
    var output = $("#qr-code");
    var qrtext = "req-" + $("#read-qrtext").attr("alt");
    showQRcode(output, qrtext);
    window.print();
    location.href = location.href;
}

function openScannWindow() {
    $(".take-scann-from-document").click(function() {
        var waitnumber = $(this).attr("alt");
        var host = "53.16.250.30:30447";
        window.open(
            "http://" + host + "/scanaction.html?waitnumber=" + waitnumber,
            "nW",
            "width=1000 height=800"
        );
    });
}
$(function() {
    var output = $("#qr-code");
    if (output.length > 0) {
        var qrtext = "req-" + $("#read-qrtext").attr("alt");
        showQRcode(output, qrtext);
    }
    openScannWindow();

})
</script>
<div class="col-12 col-md-9 col-xl-6 bg-light pt-1 p-0 <?=$hideLanguagMask?> border-left border-right border rounded"
    id="waitlist">
    <div class="row mb-1">
        <div class="col-12">
            <h4><?=$_SESSION['werkname'];?></h3>
                <h5><?=$form['string1']?>
            </h4>
        </div>
    </div>
    <?php if(isset($terminal_register) && $terminal_register="success"):
    $expl = explode(":",$waitnum);?>

    <div class="row justify-content-center m-2" id="show-waitnummer">
        <div class="col-12 text-center">
            <div class="alert-success p-3 rounded m-2 h5">
                <?=$form['string34']?>
            </div>
        </div>
        <div class="col-10 text-center m-3" id="print-waitnumber">
            <div class="card">
                <div class="card-header">
                    <h1><?=$knznummer?></h1>
                </div>
                <div class="card-body">
                    <h3>Wartenummer</h3>
                    <div class="row mt-3 justify-content-center">
                        <div class="col-8">
                            <div class="text-start" alt="<?=$expl[1];?>" id="read-qrtext">
                                <span class="display-1 font-weight-bold"><?=$expl[1];?></span> <span class="float-end"
                                    id="qr-code"></span>
                            </div>
                        </div>

                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button
                                class="btn btn-lg btn-primary text-light p-3 ps-5 pe-5 take-scann-from-document fs-3"
                                alt="<?=$expl[1];?>&<?=$knznummer?>">
                                <?=$form['string32']?></button>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-start">
                    <p>Achtung! </p>
                    <p class="small"><span class="small">
                            Bitte nehmen Sie an der Pforte ein Kommunikationsgerät (Smartphone) und tragen Sie die
                            generierte Wartenummer im entspechenden Eingabefeld ein.
                            Ihr digitaler Durchlaufschein wird angezeigt. </span>
                    <p><span class="small">Führen Sie dieses Gerät mit sich mit und zeigen Sie es bei Bedarf dem
                            Personal. Kommunikationsgerät bei dem Verlassen des Werksgeländes an der Pforte abzugeben!
                    </p>
                    </span>
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-2 mb-3 border-top pt-1">
        <div class="col-6">
            <!-- <a href="#" class="btn btn-success take-scann-from-document" alt="<?=$expl[1];?>"><?=$form['string32']?></a> -->
        </div>
        <div class="col-6 text-right">
            <a href="class/action.php?oldsession=reset&return=wea"
                class="btn btn-success text-white"><?=$form['string33']?></a>
        </div>
    </div>
</div>
<script>
setTimeout(function() {
    location.href = "class/action.php?oldsession=reset&return=wea";
}, 60000)
</script>

<?php exit; else:?>
<div class="row overflow-auto mt-2" id="runleaf" alt="wea">
    <div class="d-flex justify-content-center mt-5 pt-5">
        <span class="h4 text-black-50 d-block">Lade Übersicht &nbsp;</span>
        <div class="spinner-border d-block" role="status"><span class="sr-only">Loading...</span>
        </div>
    </div>
</div>
<?php endif; ?>
</div>