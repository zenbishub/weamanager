<?php
$setting="";
extract($_REQUEST);
require_once 'class/Controller.php';
$controller = new Controller();
$controller->checkSession();
$BMIs = $controller->getBMIData();
$PRDATA = $controller->getPersonalData();
$ReminderDATA = $controller->getReminderData();
$unloadStellen = $controller->getUnloadPlaceData();
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>WEA Einstellungen</title>
    <link rel="stylesheet" href="vendor/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendor/base/vendor.bundle.base.css">
    <link rel="stylesheet" href="vendor/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom.css">
    <link rel="shortcut icon" href="images/favicon.png" />
    <script>
    var weamanageruser = "<?=$_SESSION['weamanageruser']?>"
    var base = "<?=$controller->base?>";
    </script>
</head>

<body>
    <div class="container-scroller">
        <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top p-0 ps-3 pe-3">
            <div id="my-nav" class="collapse navbar-collapse">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?=$controller->base?>"><span class="font-large"><i
                                    class="ti-arrow-left"></i></span></a>
                    </li>
                </ul>
                zurück
            </div>
            <i class="ti-settings"></i> <span class="ps-2">Einstellungen</span>
        </nav>
        <div class="container-fluid page-body-wrapper">
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item">
                        <a class="nav-link" href="?setting=personal">
                            <i class="ti-user menu-icon"></i>
                            <span class="menu-title">Personal</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?setting=betriebsmittel">
                            <i class="ti-view-list-alt menu-icon"></i>
                            <span class="menu-title">Betriebsmittel</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?setting=entladestellen">
                            <i class="ti-view-list-alt menu-icon"></i>
                            <span class="menu-title">Entladestellen</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?setting=reminder">
                            <i class="ti-write menu-icon"></i>
                            <span class="menu-title">Erinnerungen</span>
                        </a>
                    </li>
                    <?php if(isset($_SESSION['weamanager_roll']) && $_SESSION['weamanager_roll']=="SAdmin"):?>
                    <li class="nav-item">
                        <a class="nav-link" href="?setting=admintool">
                            <i class="ti-write menu-icon"></i>
                            <span class="menu-title">App on/off</span>
                        </a>
                    </li>
                    <?php endif?>
                </ul>
            </nav>
            <!-- partial -->
            <div class="main-panel">
                <div class="content-wrapper">
                    <?php
                    if(file_exists("content/".$setting.".php")):
                        include 'content/'.$setting.'.php';
                    endif?>

                </div>
            </div>
        </div>
    </div>
    <?php include 'content/imagemodal.php';?>
    <?php include 'content/editmodal.php';?>
    <?php $controller->modal("createQRcode","");?>
    <script src="vendor/base/vendor.bundle.base.js"></script>
    <script src="vendor/jquery-ui/jquery-ui.js"></script>
    <script src='vendor/jquery/jquery.qrcode.min.js'></script>
    <script src="js/custom.js?t=<?=time()?>"></script>
    <script src="js/scanner.js?s=<?=time()?>"></script>
    <script src="js/reminder.js?t=<?=time()?>"></script>
</body>

</html>