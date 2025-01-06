<?php
spl_autoload_register(function ($class_name) {
    include 'class/'.$class_name . '.php';
});
$showContent    ="d-none";
$add_manauftrag = null;
$search         =null;
$o          = new Controller;
$myportview = $o->getMyPortView();
$evochat    = new Evochat($_SESSION['werknummer']);
$lieferant  = new Lieferant($o->scanFolder);
$verlauf    = new Verlauf;
$sapAvis    = filemtime("db/".$_SESSION['werknummer']."/extern/sapsource/AvisSAP.xlsx");

extract($_GET);
if(!empty($view)){
    $showContent=null;
}
$o->checkSession();
$evochat->createStucture();
$places             = $o->getLocations();
$lfrid              = $o->getLiferantenIDs();
$rfnum              = $o->getRFID();
$firmenname         = $o->firmenListe();
$kennzeichen        = $o->kennZeichenListe();
$getOrderList       = $o->getOrderListToday();
$getOrderListTen    = $o->getOrderListTotal(846000);
$getOrderListTwenty = $o->getOrderListTotal(25380000); //25380000 30 Tage
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>TMI Manager</title>
    <link rel="stylesheet" href="vendor/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendor/base/vendor.bundle.base.css">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/datatables/datatables.min.css" rel="stylesheet">
    <link href="vendor/jquery-ui/jquery-ui.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/custom.css?<?=time()?>" rel="stylesheet">
    <link href="css/iframe.css?<?=time()?>" rel="stylesheet">
    <link href="css/daimler.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.png" />
    <script>
    var weamanageruser = "<?=$_SESSION['weamanageruser']?>";
    var base = "<?=$o->base?>";
    </script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark db-petrol20 fixed-top">
        <a class="navbar-brand ps-3 d-none d-md-block" href="<?=$o->base?>">TMI Daimler Buses | <span
                class="small"><?=$_SESSION['werkname']."(".$_SESSION['werknummer']."/".$_SESSION['abteilung'].") / ".$_SESSION['INUMMER']?>
                <span class="small"><?=$_SESSION['weamanager_access']?></span></span></a>
        <ul class="navbar-nav ms-auto me-0 me-md-3 my-2 my-md-0">
            <li class="nav-item">
                <a href="#" class="nav-link text-white me-3" title="Hilfe" data="Steuer-Dashboard_web"
                    id="show-help-box"><i class="ti-help"></i></a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="?view=verlauf" class="nav-link text-white me-3" title="Verlauf öffnen">
                    <i class="fa fa-history"></i>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link text-white me-3" data="&" id="send_to_evochat" title="Chat öffnen"><i
                        class="ti-location-arrow"></i></a>
            </li>
            <li class="nav-item dropdown me-3 d-none d-md-block">
                <a href="?kpi" class="nav-link text-white"><i class="ti-bar-chart-alt"></i></a>
            </li>
            <li class="nav-item dropdown me-3 d-none d-md-block">
                <a class="nav-link dropdown-toggle text-white" id="navbarDropdown" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="ti-world"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="#!">Deutsch</a></li>
                    <li><a class="dropdown-item" href="#!">Français</a></li>
                    <li><a class="dropdown-item" href="#!">Türkçe</a></li>
                    <li><a class="dropdown-item" href="#!">čeština</a></li>
                    <li><a class="dropdown-item" href="#!">Englisch</a></li>
                    <li>
                        <hr class="dropdown-divider" />
                    </li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link text-white dropdown-toggle" id="navbarDropdown" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item d-none d-md-block" href="settings?setting=betriebsmittel"><i
                                class="ti-settings"></i>
                            Einstellungen</a></li>
                    <li><a class="dropdown-item" href="class/action.php?logout=1"><i class="ti-power-off"></i>
                            Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div class="p-1" id="showhidenavi"></div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-1 pt-2">
                <div class="card p-1">
                    <div class="card-header p-2 d-none d-lg-block">
                        <span><a href="<?=$o->base?>" class="btn btn-light border p-1 " id="go-home"
                                title="go to Home"><i class=" ti-desktop"></i></a></span>
                        <span><button class="btn btn-light border p-1" id="change-viewport" title="customize View"><i
                                    class="ti-layout"></i></button></span>
                        <span><a href="?search" class="btn btn-light border p-1 d-none d-md-inline-block"
                                id="open-search-page" title="go to Archiv"><i class="ti-search"></i></a></span>
                        <span><a class="btn btn-sm btn-light border p-1 d-none d-md-inline-block" id="btn-whoisonline"
                                title="Anzeige wer Online ist"><i class="ti-user"></i></a></span>
                        <span><a class="btn btn-sm btn-light border p-1 d-none d-md-inline-block"
                                data-index="<?=date("d.m.y")?>" id="btn-dailychart"
                                title="Auslastung, Visualisierung"><i class="fas fa-chart-bar"></i></a></span>

                        <button class="btn btn-sm btn-primary p-1 float-end text-light d-md-inline-block"
                            id="btn-showhidenavi"><i class="fas fa-compass"></i></button>
                    </div>
                    <div class="card-body p-0 mb-4" id="main-viewport">
                        <?php if(!isset($search) && !isset($kpi)):?>
                        <div class="row justify-content-center mt-2" id="workspace">
                            <?php
                                foreach($myportview as $column):
                                    include 'content/column_'.$column.'.php';
                                endforeach;
                            ?>
                        </div>
                        <?php endif;
                                if(isset($search)):
                                    include 'content/mainsearch.php';
                                endif;
                                if(isset($kpi)):
                                ?>
                        <iframe src="statistik.php?t=<?=time()?>" id="iframe-kpi"></iframe>
                        <?php endif?>
                    </div>
                </div>
            </div>
        </main>
        <button type="button" data="&" id="btn-send_to_evochat"
            class="btn btn-primary p-2 btn-rounded text-light btn-icon message-item"><i
                class="ti-comment-alt"></i></button>
        <div class="card" id="camera-frame">
            <div class="card-body p-1 text-center" id="camera-frame-body">
                <img alt="stream" class="img-fluid" id="camera-frame-image" data="<?=$data?>">
            </div>
        </div>
        <footer class="py-1 bg-light mt-auto fixed-bottom border-top">
            <div class="container-fluid px-1 small">
                <span id="statusleiste" class="ps-2">
                    <?php
                    $customFilter="";
                    if(isset($_SESSION['setLKWfilter'])):
                        $customFilter = implode(" | ",$o->userFilter[$_SESSION['setLKWfilter']]);
                        ?>
                    <?=$_SESSION['setLKWfilter']?> /
                    <?php endif;?>
                    <?=$_SESSION['abteilung']?>
                    <?=$customFilter;?>
                    <a
                        href="mailto:wjatscheslaw.hazenbiller@daimlertruck.com?subject=Eine gute Idee für TMI ist immer willkommen">Verbesserungen
                        zu TMI</a>
                </span>
                <div class="float-end small">
                    <span class="text-muted" id="data-entries"></span> |
                    <span class="text-muted" id="runtimer"></span> |
                    <span class="text-muted">Copyright &copy;</span>
                    <span class="text-muted"><?=phpversion()?></span>
                </div>
            </div>
        </footer>
    </div>
    </div>
    <?php include 'content/settargetplacemodal.php'?>
    <?php include 'content/messagemodal.php'?>
    <?php include 'content/setschippermodal.php'?>
    <?php include 'content/imagemodal.php'?>
    <?php include 'content/evochatmodal.php'?>
    <?php include 'content/preregistmodal.php'?>
    <?php include 'content/createqrcodemodal.php'?>
    <?php include 'content/reklamationmodal.php'?>
    <?php include 'content/diversemodal.php'?>
    <?php include 'content/ma-auftrag-modal.php'?>
    <?php include 'content/modal.php'?>
    <?php $evochat->evochatLastMessageBox($_SESSION['weamanageruser']);?>
    <?php $lieferant->alert($upload_information);?>
    <?php $o->modal("toParkingOverview","modal-fullscreen");?>
    <?php $o->modal("toVoranmeldungOverview","modal-fullscreen");?>
    <?php $o->modal("cardInfo","");?>
    <?php $o->modal("preRegisterInfo","modal-xl");?>
    <?php $o->modal("toVoranmeldungUpload",null);?>
    <?php $o->modal("reminder",null);?>
    <?php $o->modal("setIncomminTime",null);?>
    <?php $o->modal("sendtonextstep",null)?>
    <?php $o->modal("staplerAufgaben","modal-xl")?>
    <?php $o->modal("helpdesk","modal-fullscreen")?>
    <?php $o->modal("whoishere","modal-xl")?>
    <?php $o->modal("modal-iframe","modal-xl");?>
    <?php $o->alertModal();?>
    <?php if($add_manauftrag=="success"):$o->Alert("success","Aktion erfolgreich");endif;?>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/base/vendor.bundle.base.js"></script>
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="vendor/jquery-ui/jquery-ui.js"></script>
    <script src="js/form.min.js"></script>
    <script src="js/jq-touch.js"></script>
    <script src="vendor/datatables/datatables.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/todolist.js"></script>
    <script src='vendor/jquery/jquery.qrcode.min.js'></script>
    <script src="js/custom.js?s=<?=time()?>"></script>
    <script src="js/scanner.js?s=<?=time()?>"></script>
    <script src="js/frachten.js?s=<?=time()?>"></script>
    <script src="js/is_connection.js?s=<?=time()?>"></script>
    <script src="js/reminder.js?s=<?=time()?>"></script>
    <script src="js/form.async.js?s=<?=time()?>"></script>
    <script src="js/wea-timer.js?s=<?=time()?>"></script>
</body>

</html>