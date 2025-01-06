<?php
spl_autoload_register(function ($class_name) {
    include 'class/'.$class_name . '.php';
});
$o          = new Controller();
$o->appOnOff();
$lieferant  = new classPublic($o->scanFolder);

$evochat    = new Evochat($_SESSION['werknummer']);
$extern     = new StaplerAufgaben();
$resourse = $_SESSION['weamanageruser'];
extract($_GET);
$o->checkSession();

$lieferant->appname ="stapler";
$lieferant->setAdditionTask();
$lfrid          = $o->getLiferantenIDs();
$firmenname     = $o->firmenListe();
$kennzeichen    = $o->kennZeichenListe();
$checkAvailable = $lieferant->checkRessourseStatus($resourse); 

switch($_SESSION['INUMMER']){
    case 1:
        $colLeft = "col-md-4";
        $colRight ="col-12 col-md-8";
    break;
    default:
        $colLeft = "col-12";
        $colRight ="col-12";
    break;
}

?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
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
    <link href="css/style.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/custom.css?<?=time()?>" rel="stylesheet">
    <link href="css/iframe.css?<?=time()?>" rel="stylesheet">
    <link href="css/daimler.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.png" />
    <script>
    var weamanageruser = "<?=$_SESSION['weamanageruser']?>"
    </script>
</head>

<body class="sb-nav-fixed bg-light">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark db-petrol20 pe-0">
        <a class="navbar-brand ps-3" href="stapler"><?=$_SESSION['weamanageruser'];?>
            <?php if(isset($_SESSION['schichtleiter']) && $_SESSION['schichtleiter']!=""):?>
            / <span class="small"><?=$_SESSION['schichtleiter']?></span>
            <?php endif;?>
            <?php if(isset($_SESSION['adittionalJob'])):?>
            / <span class="small"><?=ucfirst($_SESSION['adittionalJob'])?></span>
            <?php endif;?>
        </a>
        <!-- Navbar send_to_evochat-->
        <ul class="form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
            <?php if($_SESSION['INUMMER']==1):?>
            <li class="nav-item dropdown me-3">
                <a class="nav-link text-light font-large p-0 ps-1 pe-1 pointer dropdown-toggle d-flex justify-content-center align-items-center"
                    id="messageDropdown-ruecken" href="#" data-bs-toggle="dropdown" aria-expanded="true">
                    + Aufgaben
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown mt-3"
                    aria-labelledby="messageDropdown-ruecken" data-bs-popper="none">
                    <a href="rueckenandienung" class="dropdown-item border-bottom d-flex mb-2 pe-2 additional-job">
                        <h5 class="ellipsis font-weight-normal">Rückenandienung</h5>
                    </a>
                    <a href="norueckenandienung" class="dropdown-item d-flex pe-2 additional-job">
                        <h5 class="ellipsis font-weight-normal">keine</h5>
                    </a>
                </div>
            </li>
            <li class="nav-item dropdown me-2">
                <a class="nav-link text-light font-large p-0 ps-1 pe-1 pointer dropdown-toggle d-flex justify-content-center align-items-center"
                    id="messageDropdown-userstatus" href="#" data-bs-toggle="dropdown" aria-expanded="true">
                    <?=$checkAvailable?>
                </a>
                <div class="dropdown-menu dropdown-menu-right navbar-dropdown mt-3"
                    aria-labelledby="messageDropdown-userstatus" data-bs-popper="none">
                    <a href="notavailable" class="dropdown-item border-bottom d-flex mb-2 alert-danger user-status">
                        <span class="mt-1 me-2"><i class="ti-na"></i></span>
                        <h5 class="ellipsis font-weight-normal">nicht verfügbar</h5>
                    </a>
                    <a href="available" class="dropdown-item d-flex alert-success user-status">
                        <span class="mt-1 me-2"><i class="ti-user"></i></span>
                        <h5 class="ellipsis font-weight-normal">verfügbar</h5>
                    </a>
                </div>
            </li>
            <li class="nav-item border rounded ps-1 pe-1 me-2">
                <a class="nav-link text-light font-large p-0 ps-1 pe-1 pointer"
                    data="<?=$_SESSION['weamanageruser'];?>&Zentrale" id="send_to_evochat"><i
                        class="ti-location-arrow"></i></a>
            </li>
            <?php endif;?>
            <li class="nav-item border rounded ps-1 pe-1 me-2">
                <a class="nav-link text-light font-large p-0 ps-1 pe-1 pointer" id="btn-location-reload"><i
                        class="ti-reload"></i></a>
            </li>
            <li class="nav-item border rounded ps-1 pe-1">
                <a class="nav-link text-light font-large p-0 ps-1 pe-1 pointer" id="btn-session-reload"><i
                        class="ti-power-off"></i></a>
            </li>
        </ul>
    </nav>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-2 pt-4">
                <div class="row justify-content-center">
                    <div class="col-12 p-0 bg-light border-left border-right" id="waitlist">
                        <div class="row mt-5 justify-content-center">
                            <div class="col-12 p-0 col-xl-8 text-center">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active fs-5" id="one-tab" data-bs-toggle="tab"
                                            data-bs-target="#one" type="button" role="tab" aria-controls="one"
                                            aria-selected="true">Startpage</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link fs-5" id="two-tab" data-bs-toggle="tab"
                                            data-bs-target="#two" type="button" role="tab" aria-controls="two"
                                            aria-selected="false">LKW-Manager</button>
                                    </li>
                                    <?php if($_SESSION['INUMMER']!=3):?>
                                    <li class="nav-item d-none d-md-block" role="presentation">
                                        <button class="nav-link fs-5" id="three-tab" data-bs-toggle="tab"
                                            data-bs-target="#three" type="button" role="tab" aria-controls="three"
                                            aria-selected="false">Sitzfertigung</button>
                                    </li>
                                    <li class="nav-item d-none d-md-block" role="presentation">
                                        <button class="nav-link fs-5" id="four-tab" data-bs-toggle="tab"
                                            data-bs-target="#four" type="button" role="tab" aria-controls="four"
                                            aria-selected="false">Routenplan</button>
                                    </li>
                                    <?php endif?>
                                    <li class="nav-item ms-auto" role="">
                                        <iframe src="content/wificheck" width="60px" height="40px"></iframe>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="one" role="tabpanel"
                                        aria-labelledby="one-tab">
                                        <div class="row justify-content-center pt-3">
                                            <?php if(!empty($_SESSION['schichtleiter'])):?>
                                            <div class="<?=$colLeft?> p-0 pe-3 border-right">
                                                <h5>Schichtleiter-Info</h5>
                                                <hr>
                                                <div class="table-responsive p-0" id="coming-next">
                                                    <div class="d-flex justify-content-center mt-5 pt-5">
                                                        <span class="h4 text-black-50 d-block">Lade Übersicht
                                                            &nbsp;</span>
                                                        <div class="spinner-border d-block" role="status"><span
                                                                class="sr-only">Loading...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif;?>
                                            <div class="<?=$colRight?> overflow-auto text-left"
                                                id="column-warteschlange" alt="stapler"
                                                data="<?=$_SESSION['INUMMER']?>">
                                                <div class="d-flex justify-content-center mt-5 pt-5">
                                                    <span class="h4 text-black-50 d-block">Lade Übersicht &nbsp;</span>
                                                    <div class="spinner-border d-block" role="status"><span
                                                            class="sr-only">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php //if($_SESSION['INUMMER']!=3):?>
                                    <div class="tab-pane fade" id="two" role="tabpanel" aria-labelledby="two-tab">
                                        <div class="row m-0 p-0 border-top justify-content-center" alt="weamanager">
                                            <div class="p-0 col-12">
                                                <iframe src="../../weamanager/de/" width="100%"
                                                    class="frame-height"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                    <?php //endif?>
                                    <div class="tab-pane fade" id="three" role="tabpanel" aria-labelledby="three-tab">
                                        <div class="row m-0 p-0 border-top justify-content-center" alt="andienung">
                                            <div class="p-0 col-12">
                                                <iframe width="100%" class="frame-height" id="three-content"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="four" role="tabpanel" aria-labelledby="four-tab">
                                        <div class="row m-0 p-0 border-top justify-content-center">
                                            <div class="p-0 col-12">
                                                <iframe width="100%" class="frame-height" id="four-content"></iframe>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if(isset($_REQUEST['job'])):?>
                            <!-- <div class="row m-0 mt-2 mb-4 pb-4 p-0 border-top justify-content-center" alt="stapler">
                                <div class="p-0 col-10">
                                    <iframe
                                        src="../../nu/sitzfertigung/komzone/andienung/?selectplatz=sitzandienung&koordinator=sitzandienung&job=sitzandienung"
                                        width="100%" class="frame-height"></iframe>
                                </div>
                            </div> -->
                            <?php endif;?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <div id="uhrzeit" class="shadow d-none d-md-block"></div>
    <div id="status" class="fixed-bottom p-1 border-top bg-status">
    </div>
    <!-- <audio src="assets/sound/scanok.mp3" id="mysound" autoplay></audio> -->
    <?php include 'content/modal.php'?>
    <?php include 'content/setschippermodal.php'?>
    <?php include 'content/imagemodal.php'?>
    <?php include 'content/evochatmodal.php'?>
    <?php include 'content/reklamationmodal.php'?>
    <?php include 'content/diversemodal.php';?>
    <?php $evochat->evochatLastMessageBox($_SESSION['weamanageruser']);?>
    <?php $o->alertModal();?>
    <?php $o->modal("sendtonextstep",null)?>
    <?php $extern->modalExtern("offlineladePorzess");?>
    <?php $extern->modalExtern("confirmLadeProzess");?>
    <?php $extern->modalExtern("confirmBeladen");?>
    <?php $extern->modalExtern("startBeladen", "modal-fullscreen");?>
    <?php $extern->modalExtern("openSeqLagerFull","modal-fullscreen");?>
    <?php $extern->modalExtern("confirmPlaceInseqLager","shadow-lg");?>
    <?php $extern->modalExtern("checkInhalt","modal-fullscreen");?>
    <?php $o->modal("connectionOffline",null)?>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/base/vendor.bundle.base.js"></script>
    <script src="vendor/jquery-ui/jquery-ui.js"></script>
    <script src="js/form.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="js/stapler.js?s=<?=time()?>"></script>
    <script src="js/stapler-extern.js?s=<?=time()?>"></script>
    <script src="js/form.async.js?s=<?=time()?>"></script>
    <script src="js/uhrzeit.js?s=<?=time()?>"></script>
    <script src="js/is_connection.js?s=<?=time()?>"></script>

</body>

</html>