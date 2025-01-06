<?php
spl_autoload_register(function ($class_name) {
    include 'class/'.$class_name . '.php';
});

$hideMask="";
$hideLanguagMask = "d-none";
$hideStartMask="d-none";
$soundmodus = 1;
$requestNummer=null;
extract($_GET);
$o = new Controller();
$o->appOnOff();
$lieferant  = new classPublic($o->scanFolder);

$verlauf    = new Verlauf();

$lieferant  ->  appname ="wea";
$form        = $lieferant->selectFormLanguage();
$alerts      = $lieferant->selectAlertLanguage();
$inactive    = $lieferant->checkInactiveTime($lieferant->inactivetimeStart, $lieferant->inactivetimeEnd);
$dropOne     = $lieferant->selectDropDownOneLanguage();
$dropTwo     = $lieferant->selectDropDownTwoLanguage();
$lfrid       = $o->getLiferantenIDs();
$rfnum       = $o->getRFID("");
$firmenname  = $o->firmenListe();
$kennzeichen = $o->kennZeichenListe();
$soundmodus  = $o->soundModus();
$plants      = $o->selectPlant();
$ladungDaten = $o->selectLadungDaten();
$frzTypDaten = $o->selectFRZDaten();
$lieferant   ->  setScannerNummer();
//print_R($_SESSION);
if(empty($_SESSION['frzlaufnummer'])){
    $hideMask = "d-none";
}
if(empty($_SESSION['start_prozess'])){
    $hideLanguagMask = "d-block";
}
if(!empty($_SESSION['start_prozess'])){
    $hideStartMask="d-block";
}
if(!empty($_SESSION['frzlaufnummer']) && !empty($_SESSION['start_prozess'])){
    $hideMask = "d-block";
    $hideStartMask="d-none";
    $hideLanguagMask="d-block";
}
switch($soundmodus){
    case 1:
        $soundicon = "<i class='fas fa-volume-up'></i>";
        break;
    case 2:
        $soundicon = "<i class='fas fa-volume-mute'></i>";
        break;
}
if(isset($_REQUEST['requestNummer'])){
    $_SESSION['start_prozess']=time();
}

?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1, minimum-scale=1.0, maximum-scale=1.0, minimal-ui" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name='theme-color' content='#00566a'>
    <meta name='msapplication-navbutton-color' content='#00566a'>
    <meta name='apple-mobile-web-app-status-bar-style' content='#00566a'>
    <title>TMI Manager</title>
    <link rel="stylesheet" href="vendor/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendor/base/vendor.bundle.base.css">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/jquery-ui/jquery-ui.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/custom.css?t=<?=time()?>" rel="stylesheet">
    <link href="css/daimler.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.png" />
    <script>
    var rfnum = null;
    var jsonLanguage = '<?=$alerts['string1']?>';
    var alarmText = '<?=$alerts['string2']?>';
    var base = '<?=$o->base?>wea';
    </script>
</head>

<body class="sb-nav-fixed bg-secondary">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark db-petrol20">
        <a class="navbar-brand ps-2 d-none d-md-block" href="wea">Dailmer Buses
        </a>

        <ul class="ps-0 pe-0 ms-auto me-0 my-2 my-md-0 d-flex">
            <li class="nav-item border rounded d-none d-md-block me-1">
                <a class="nav-link text-light  pointer font-large p-1 ps-2 pe-2" id="live-view">
                    <i class="fas fa-video"></i>
                </a>
            </li>


            <li class="nav-item border rounded me-1">
                <a class="nav-link text-danger pointer font-large p-1 ps-2 pe-2" id="wifi-status">
                    <i class="fas fa-wifi"></i>
                </a>
            </li>
            <li class="nav-item border rounded me-1">
                <a class="nav-link text-light pointer font-large p-1 ps-3 pe-3" id="informations">
                    <i class="fas fa-info"></i>
                </a>
            </li>
            <li class="nav-item border rounded me-1">
                <a class="nav-link text-light pointer font-large p-1 ps-2 pe-2" id="battery-status">...</a>
            </li>

            <li class="nav-item  dropdown border rounded ps-1 pe-1 me-1">
                <a class="nav-link font-large p-1 dropdown-toggle text-light" id="navbarDropdown" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti-world mx-0"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="navbarDropdown">
                    <?php foreach($lieferant->arrayOfLanguages as $key=>$value):?>
                    <li class="pt-0 pb-0">
                        <a class="dropdown-item h6 p-1 m-0 border-bottom"
                            href="class/action?set_language=<?=$key?>&return_uri=wea"> <img
                                src="assets/img/flage<?=$key?>.jpg" class="img-thumbnail img-mini">
                            <?=ucfirst($value)?></a>
                    </li>
                    <?php endforeach;?>
                </ul>
            </li>

            <li class="nav-item border rounded me-1">
                <a href="<?=$o->base?>" class="nav-link text-light pointer font-large p-1 ps-2 pe-2"
                    id="btn-open-locations" data-toggle="modal" data-target="#viewerModal"><i
                        class="ti-location-pin mx-0"></i></a>
            </li>

            <li class="nav-item border rounded">
                <a class="nav-link text-light pointer font-large p-1 ps-2 pe-2" id="btn-session-reload"><i
                        class="ti-reload"></i></a>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-2 pt-4 pb-4">
                <div class="row mt-5 justify-content-center">

                    <?php if($inactive==true):?>
                    <?php include 'content/inactive.php'?>
                    <?php else:?>
                    <?php include 'content/ongate.php'?>
                    <?php include 'content/runleaf.php'?>
                    <?php include 'content/modal.php'?>
                    <?php include 'content/chatmodal.php'?>
                    <?php include 'content/locationsmodal.php';?>
                    <?php include 'content/readqrcodemodal.php';?>
                    <?php include 'content/diversemodal.php';?>
                    <?php include 'content/imagemodal.php';?>
                    <?php include 'content/alertmodal.php'?>
                    <?php include 'content/reklamationmodal.php'?>
                    <div id="geoposition-box" class="border rounded p-1 ms-2 d-md-none">
                        <div id="geoposition" class="small" data-index="<?=$_SERVER['REMOTE_ADDR']?>"></div>
                        <div id="geoposition-link"></div>
                    </div>

                    <?php endif;
                     $o->alertModal();
                     ?>
                    <?php $o->modal("sendtonextstep",null)?>
                    <?php $o->modal("connectionOffline",null)?>
                    <?php $o->modal("startvideo-modal","modal-fullscreen")?>
                    <?php $o->modal("updates",null)?>
                </div>
            </div>
        </main>
    </div>
    </div>

    <?php if(!isset($_SESSION['start_prozess']) && !isset($_SESSION['rfnum'])):?>
    <div class="row justify-content-center fixed-bottom p-2" id="col-qrcode-row">
        <div class="col-4" id="col-qrcode"></div>
    </div>
    <?php  endif;?>
    <div class="row fixed-bottom d-none" id="frame-live-view-row">
        <div class="col-3 card p-0">
            <div class="card-header"><span class="float-right pointer" id="close-live-view"><i
                        class="ti ti-close"></i></span>
            </div>
            <div class="card-body p-0" id="frame-live-view"></div>
        </div>

    </div>
    <?php $o->alertModal();?>
    <?php if(isset($_SESSION['rfnum'])):?>
    <script>
    rfnum = <?=$_SESSION['rfnum']?>
    </script>
    <?php endif;?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/batteryAPI.js?s=<?=time()?>"></script>
    <script src="js/geolocation.js?s=<?=time()?>"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/base/vendor.bundle.base.js"></script>
    <script src="vendor/jquery-ui/jquery-ui.min.js"></script>
    <script src="js/form.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="js/dgsvo.js"></script>
    <script src="js/html5-qrcode.min.js"></script>
    <script src="js/public.js?s=<?=time()?>"></script>
    <script src="js/jq-touch.js"></script>
    <script src="js/scanbyqrcode.js?s=<?=time()?>"></script>
    <script src="js/is_connection.js?s=<?=time()?>"></script>
    <script src="js/startvideo.js?s=<?=time()?>"></script>

    <?php

if(!empty($requestNummer)):?>
    <script>
    var Nummer = "<?=$requestNummer?>";
    $(function() {
        completeFieldsByScan(Nummer)
    });
    </script>
    <?php endif;?>
</body>

</html>