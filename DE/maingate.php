<?php
$hideElem = "d-none";
$colLeft = "col-md-6";
$colRight = "col-md-6";
$colParking = "col-md-2";
$colWerkVerkehr = "";
$colParkingPalce = "";
$hideCloseButton = "";
extract($_REQUEST);
if(isset($controls)){
    $hideElem="";
    $colLeft = "col-md-5";
    $colRight = "col-md-5";
    $colParking = "col-md-2";
}
if(isset($controls) && $controls=="custom"){
    $hideElem="";
    $colLeft = "d-none";
    $colRight = "d-none";
    $colParking = "row p-3";
    $colWerkVerkehr = "col-2";
    $colParkingPalce = "col-10";
    $hideCloseButton= "d-none";

}
spl_autoload_register(function ($class_name) {
    include 'class/'.$class_name . '.php';
});
$o = new Controller();
$o->checkSession();

$getOrderList   = $o->getOrderList("");
$count          = $o->maxIDinOrerList("")+1;
$gate           = new Maingate();
$evochat        = new Evochat($_SESSION['werknummer']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>TMI Monitor on Gate</title>
    <link rel="stylesheet" href="vendor/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendor/base/vendor.bundle.base.css">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/jquery-ui/jquery-ui.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link href="css/custom.css" rel="stylesheet">
    <link href="css/daimler.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.png" />
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row db-petrol20">
        <div class="navbar-menu-wrapper d-flex ps-1 align-items-center justify-content-start ">
            <ul class="navbar-nav navbar-nav-left">
                <li class="nav-item" title="Aktualisieren">
                    <a href=" maingate" class="ps-0 nav-link text-light font-large"><i class="ti-reload"></i></a>
                </li>
                <li class="nav-item" title="Chat öffnen">
                    <a href="#" class="nav-link text-white font-large" data="&" id="send_to_evochat"><i
                            class="ti-location-arrow"></i></a>
                </li>
                <li class="nav-item" title="Steuerpanel öffnen">
                    <a href="<?=$o->base?>maingate?controls" class="nav-link text-white font-large"
                        id="show-control-buttons"><i class="fas fa-sliders-h"></i></a>
                </li>
                <li class="nav-item" title="Weg zu Entladestellen">
                    <a href="<?=$o->base?>" class="nav-link text-light font-large" id="btn-open-locations"
                        data-bs-toggle="modal" data-bs-target="#viewerModal"><i class="ti-location-pin mx-0"></i></a>
                </li>
                <li class="nav-item" title="Endgeräte Information">
                    <a href="#" class="nav-link text-light font-large" id="btn-open-scannerinfo" data-bs-toggle="modal"
                        data-bs-target="#scannerInfo"><i class="ti-mobile mx-0"></i></a>
                </li>
            </ul>
            <ul class="ml-auto navbar-nav">
                <li class="nav-item nav-profile dropdown ms-3">
                    <a class="nav-link dropdown-toggle text-light font-large" href="#" data-bs-toggle="dropdown"
                        id="profileDropdown" aria-expanded="false">
                        <i class="ti-user mx-0"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
                        <a class="dropdown-item" href="?controls=custom" target="_blank"><i class="ti-desktop"></i>
                            Layout</a>
                        <a class="dropdown-item" href="class/action.php?logout=1&appname=maingate"><i
                                class="ti-power-off"></i> Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <div class="p-1" id="showhidenavi"></div>
    <div class="container-scroller">
        <div class="container-fluid p-0">
            <div class="content-wrapper p-0">
                <div class="row justify-content-center">
                    <div class="<?=$colLeft?> grid-margin stretch-card p-0" id="monitor-ausfahrt">
                        <div class="card">
                            <div class="card-header h2 bg-primary text-light text-center pe-2 shadow">
                                Ausfahrt
                                <span class="btn btn-light float-end p-2" id="hide-monitor-ausfahrt"><i
                                        class="ti-close"></i></span>
                            </div>
                            <div class="card-body overflow-auto p-1" id="gate-column-left">
                                <div class="row justify-content-center m-4 p-4">
                                    <div class="col-1">
                                        <div class="spinner-border" role="status"><span
                                                class="sr-only">Loading...</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="<?=$colRight?> grid-margin stretch-card p-0" id="monitor-einfahrt">
                        <div class="card">
                            <div class="card-header h2 bg-primary text-light text-center pe-2 shadow">
                                Einfahrt
                                <span class="btn btn-light float-end p-2" id="hide-monitor-einfahrt"><i
                                        class="ti-close"></i></span>
                            </div>
                            <div class="card-body overflow-auto p-1" id="gate-column-right">
                                <div class="row justify-content-center m-4 p-4">
                                    <div class="col-1">
                                        <div class="spinner-border" role="status"><span
                                                class="sr-only">Loading...</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="<?=$colParking?> grid-margin  mb-0  bg-white p-2 border <?=$hideElem?>"
                        id="monitor-werksverkehr">
                        <div class="card p-0 border-0 <?=$colWerkVerkehr?> stretch-card">
                            <div class="card-header h5 bg-warning  text-center p-2">Werksverkehr
                                <span class="btn btn-light float-end p-1 <?=$hideCloseButton?>"
                                    id="hide-monitor-werksverkehr"><i class="ti-close"></i></span>
                            </div>
                            <div class="card-body overflow-auto" id="gate-column-right-top">
                                <div class="row justify-content-center mt-2 mb-2">
                                    <div alt="werksverkehr" title="<?=$count?>" data="<?=$controls?>"
                                        id="werksverkehr-button"
                                        class="column-werksverkehr col-12 p-3 text-center h5 border rounded-2 bg-danger text-white pointer">
                                        <i class="ti-truck"></i> Pforte passiert
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 p-0" id="werksverkehr-box">
                                        Werksverkehr ...
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card border-0 p-0 <?=$colParkingPalce?> stretch-card alert-warning">
                            <div class="card-header h5 bg-warning text-black text-center pe-1">
                                Am Parkplatz
                            </div>
                            <div class="card-body overflow-auto p-1" id="gate-column-right-bottom" alt="<?=$controls?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    <div class="card <?=$hideElem?> d-none" id="gate-control-buttons">
        <div class="card-header">
            Schrankensteuerung
            <span class="btn btn-light float-end p-1" id="hide-control-buttons"><i class="ti-close"></i></span>
        </div>
        <div class="card-body p-1">
            <div class="row">
                <div class="col-6 p-1 text-center">
                    <h4 class="text-center">Einfahrt</h4>
                    <button class="btn btn-success text-light controls" alt="e-open">öffnen</button>
                    <button class="btn btn-success text-light controls" alt="e-close">schließen</button>
                </div>
                <div class="col-6 p-1 text-center">
                    <h4 class="text-center">Ausfahrt</h4>
                    <button class="btn btn-success text-light controls" alt="a-open">öffnen</button>
                    <button class="btn btn-success text-light controls" alt="a-close">schließen</button>
                </div>
            </div>
        </div>
    </div>
    <div class="card display-1 rounded font-weight-bold" id="timebox">
        <div class="card-body p-2" id="showzeit"></div>
    </div>
    <footer class="py-1 bg-light mt-auto fixed-bottom border-top">
        <div class="container-fluid px-1">
            Maingate <span id="location-statusbar"></span>
            <div class="float-end small">
                <span class="text-muted small ">Copyright ©</span>
            </div>
        </div>
    </footer>
    <?php include 'content/locationsmodal.php';?>
    <?php include 'content/messagemodal.php'?>
    <?php include 'content/evochatmodal.php'?>
    <?php
    $gate->modal("scannerInfo","modal-xl");
    $gate->modal("editRFnum","modal-xl");
    $gate->modal("geoInfo","");
    $evochat->evochatLastMessageBox($_SESSION['weamanageruser']);?>
    <?php $o->modal("modal-iframe","modal-xl");?>
    <?php $o->alertModal();?>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/base/vendor.bundle.base.js"></script>
    <script src="vendor/jquery-ui/jquery-ui.js"></script>
    <script src="js/form.min.js"></script>
    <script src="js/jq-touch.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/todolist.js"></script>
    <script src='vendor/jquery/jquery.qrcode.min.js'></script>
    <script src="js/pforte.js?s=<?=time()?>"></script>
    <script src="js/scanner.js?s=<?=time()?>"></script>
    <script src="js/form.async.js?s=<?=time()?>"></script>
</body>

</html>