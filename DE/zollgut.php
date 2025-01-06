<?php
spl_autoload_register(function ($class_name) {
    include 'class/'.$class_name . '.php';
});
$o = new Controller();
$extern = new Externe($_SESSION['werknummer']);
$o->checkSession();
$zollgut = new Zollgut($_SESSION['werknummer']);
$evochat = new Evochat($_SESSION['werknummer']);
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
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link href="vendor/jquery-ui/jquery-ui.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <link href="css/iframe.css" rel="stylesheet">
    <link href="css/daimler.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.png" />
    <script>
    var werknummer = "<?=$_SESSION['werknummer']?>"
    </script>
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark db-petrol20 fixed-top">
        <a class="navbar-brand ps-3" href="<?=$o->base?>zollgut">TMI Daimler Buses | <span
                class="small"><?=$_SESSION['werkname']."(".$_SESSION['werknummer'].")"?>
        </a>
        <ul class="navbar-nav ms-auto me-0 me-md-3 my-2 my-md-0">
            <li class="nav-item">
                <a href="#" class="nav-link text-white me-3" title="Hilfe" data="Steuer-Dashboard_web"
                    id="show-help-box"><i class="ti-help"></i></a>
            </li>
            <li class="nav-item">
                <a href="information" target="_blank" class="nav-link text-white me-4"><i class="ti-blackboard"></i></a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link text-white me-4" data="&" id="send_to_evochat"><i
                        class="ti-comment-alt"></i></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link text-white dropdown-toggle" id="navbarDropdown" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="class/action.php?logout=1&appname=zollgut"><i
                                class="ti-power-off"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div class="p-1" id="showhidenavi"></div>
    <div id="layoutSidenav_content">
        <main>
            <button type="button" data="&amp;" id="btn-send_to_evochat"
                class="btn btn-primary p-2 btn-rounded text-light btn-icon message-item"><i
                    class="ti-comment-alt"></i></button>
            <div class="container-fluid px-4 px-md-3 px-lg-2 pt-2">
                <div class="card p-1">
                    <div class="card-header p-2 mb-2">
                        Zollgut-Anmeldungen
                        <button class="btn btn-sm btn-primary p-1 float-end text-light" id="btn-showhidenavi"><i
                                class="fas fa-compass"></i></button>
                    </div>
                    <div class="card-body p-0" id="main-viewport">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                    data-bs-target="#home" type="button" role="tab" aria-controls="home"
                                    aria-selected="true">Arbeitsmappe</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link " id="sendungen-tab" data-bs-toggle="tab"
                                    data-bs-target="#sendungen" type="button" role="tab" aria-controls="sendungen"
                                    aria-selected="true">Sendungen</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link open-content-by-request" alt="externe-information"
                                    id="profile-tab" data-bs-toggle="tab" data-bs-target="#externe-information"
                                    type="button" role="tab" aria-controls="profile"
                                    aria-selected="false">Speditionen</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link open-content-by-request" alt="zoll-verlauf-abfertigung"
                                    id="verlauf-abfertigung-tab" data-bs-toggle="tab"
                                    data-bs-target="#zoll-verlauf-abfertigung" type="button" role="tab"
                                    aria-controls="frei" aria-selected="false">Abfertigungen</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link open-content-by-request" alt="externe-upload"
                                    id="externe-upload-tab" data-bs-toggle="tab" data-bs-target="#externe-upload"
                                    type="button" role="tab" aria-controls="frei" aria-selected="false">Upload</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <div class="row justify-content-center pt-4 pb-4" id="workspace">
                                    <div class="col-md-5 grid-margin p-0 bg-light border m-1 stretch-card">
                                        <div class="card">
                                            <div class="card-header">
                                                Am Parkplatz
                                            </div>
                                            <div class="card-body" id="zoll-warteliste">
                                                <div class="d-flex justify-content-center p-5">
                                                    <div class="spinner-border" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-5 grid-margin p-0 bg-light border m-1 stretch-card">
                                        <div class="card ">
                                            <div class="card-header">
                                                Abgefertigt
                                            </div>
                                            <div class="card-body" id="zoll-abgefertigt">
                                                <div class="d-flex justify-content-center p-5">
                                                    <div class="spinner-border" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="sendungen" role="tabpanel" aria-labelledby="sendungen-tab">
                                <iframe src="comingup?hidenavi=on" id="iframe-information"></iframe>
                            </div>
                            <div class="tab-pane fade p-4" id="externe-information" role="tabpanel"
                                data="externe-information" aria-labelledby="externe-information-tab">
                                <div class="d-flex justify-content-center p-5">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade p-4" id="zoll-verlauf-abfertigung" role="tabpanel"
                                aria-labelledby="verlauf-abfertigung-tab">
                                <div class="d-flex justify-content-center p-5">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade p-4" id="externe-upload" role="tabpanel"
                                aria-labelledby="externe-upload-tab">
                                <div class="d-flex justify-content-center p-5">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-1 bg-light mt-auto fixed-bottom border-top">
            <div class="container-fluid px-1">
                <span id="statusleiste"><?=$_SERVER['REQUEST_URI']?></span>
                <div class="float-end small">
                    <span class="text-muted small ">Copyright &copy;</span>
                </div>
            </div>
        </footer>
    </div>
    <?php include 'content/messagemodal.php'?>
    <?php include 'content/evochatmodal.php'?>
    <?php include 'content/zollactionmodal.php'?>
    <?php include 'content/imagemodal.php'?>
    <?php $zollgut->modal("cardInfo","modal-xl");?>
    <?php $zollgut->modal("changeUnloadPlant","");?>
    <?php $evochat->evochatLastMessageBox($_SESSION['weamanageruser']);?>
    <?php $o->modal("helpdesk","modal-fullscreen")?>
    <?php $o->alertModal();?>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/base/vendor.bundle.base.js"></script>
    <script src="vendor/jquery-ui/jquery-ui.js"></script>
    <script src="js/form.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/todolist.js"></script>
    <script src="js/zollgut.js?s=<?=time()?>"></script>
    <script src="js/scanner.js?s=<?=time()?>"></script>
    <script src="js/form.async.js?s=<?=time()?>"></script>
</body>

</html>