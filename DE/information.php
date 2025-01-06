<?php
spl_autoload_register(function ($class_name) {
    include 'class/'.$class_name . '.php';
});
$showContent="d-none";
$search=null;
$o      = new Controller();
$info   = new Information();
extract($_GET);
if(!empty($view)){
    $showContent=null;
}
$o->checkSession();
$lfrid          = $o->getLiferantenIDs();
$firmenname     = $o->firmenListe();
$kennzeichen    = $o->kennZeichenListe();
$getOrderList   = $o->getOrderListToday();
$onParking      = $o->getOnParking($getOrderList, "parking");
$onProzess      = $o->getOnParking($getOrderList, "process");

?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title>TMI Manager Information</title>
    <link rel="stylesheet" href="vendor/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendor/base/vendor.bundle.base.css">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="vendor/jquery-ui/jquery-ui.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/custom.css?<?=time()?>" rel="stylesheet">
    <link href="css/daimler.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.png" />
    <script>
    var weamanageruser = "<?=$_SESSION['weamanageruser']?>"
    </script>
</head>

<body class="sb-nav-fixed">
    <nav class="navbar navbar-expand navbar-dark db-petrol20">
        <a class="navbar-brand ps-3" href="<?=$o->base?>information">TMI Daimler Buses | <span
                class="small"><?=$_SESSION['werkname']."(".$_SESSION['werknummer'].")"?>
                <span class="small"><?=$_SESSION['weamanager_access']?></span></span></a>
        <ul class="navbar-nav ms-auto me-0 me-md-3 my-2 my-md-0">
            <li class="nav-item">
                <a href="?view=verlauf" class="nav-link text-white me-3">
                    <i class="fa fa-history"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link text-white dropdown-toggle" id="navbarDropdown" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="class/action.php?logout=1&appname=information"><i
                                class="ti-power-off"></i>
                            Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div class="p-1" id="showhidenavi"></div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 px-md-3 px-lg-2 pt-2">
                <div class="row">
                    <div class="col-12 col-md-8 p-1 stretch-card">
                        <div class="card border-0 p-0">
                            <div class="card-header h4 text-black text-center p-3">
                                Am Parkplatz, Neu-Ulm Werk 5
                            </div>
                            <div class="card-body overflow-auto p-1" id="on-parking">
                                <?php $info->trucksOnParking($onParking)?>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 p-1 stretch-card">
                        <div class="card border-0 p-0 stretch-card">
                            <div class="card-header h4 text-black text-center p-3">
                                im Prozess
                            </div>
                            <div class="card-body p-1" id="im-prozess">
                                <?php $info->trucksImProcess($onProzess);?>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-12 p-1">
                        <iframe src="content/timeline.php"></iframe>
                    </div> -->
                </div>
            </div>
        </main>
        <footer class="py-1 bg-light mt-auto fixed-bottom border-top">
            <div class="container-fluid px-1">
                <span id="statusleiste">Interval</span>
                <div class="float-end small">
                    <span class="text-muted small ">Copyright &copy;</span>
                </div>
            </div>
        </footer>
    </div>
    </div>
    <?php $o->modal("prio","")?>
    <?php $o->alertModal();?>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/base/vendor.bundle.base.js"></script>
    <script src="vendor/jquery-ui/jquery-ui.js"></script>
    <script src="js/form.min.js"></script>
    <script src="js/jq-touch.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/todolist.js"></script>
    <script src='vendor/jquery/jquery.qrcode.min.js'></script>
    <script src="js/information.js?s=<?=time()?>"></script>
    <script src="js/form.async.js?s=<?=time()?>"></script>
</body>

</html>