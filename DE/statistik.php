<?php
require_once 'class/Statistik.php';
$o = new Statistik();
session_start();
$monthArray = $o->monthInDatabase();
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>LKW Manager Statistik</title>
    <link rel="stylesheet" href="vendor/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendor/base/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="shortcut icon" href="images/favicon.png" />
    <script>
    var werknummer = "<?=$_SESSION['werknummer']?>";
    var monthForChart = "<?=date("m.Y")?>";
    </script>
</head>

<body>
    <div class="container-fluid p-0 m-0 stretch-card">
        <div class="content-wrapper">
            <div class="row">
                <div class="col-lg-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <nav class="navbar p-0" id="cht3">
                                <div class="d-flex">
                                    <a class="navbar-brand" href="#">
                                        <h4 class="card-title">Auslastungszeiten</h4>
                                    </a>
                                    <ul class="navbar-nav ml-auto">
                                        <li class="nav-item">
                                            <select class="form-select form-select-sm small" id="select-month">
                                                <option value="">wählen</option>
                                                <?php foreach($monthArray as $mnt):?>
                                                <option value="<?=$mnt?>"><?=$mnt?></option>
                                                <?php endforeach?>
                                            </select>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                            <canvas id="barChartTree"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <nav class="navbar p-0" id="cht1">
                                <div class="d-flex">
                                    <a class="navbar-brand" href="#">
                                        <h4 class="card-title">Zollgüter</h4>
                                    </a>
                                    <ul class="navbar-nav ml-auto">
                                        <li class="nav-item">
                                            <select class="form-select form-select-sm small" id="select-month-zoll">
                                                <option value="">wählen</option>
                                                <?php foreach($monthArray as $mnt):?>
                                                <option value="<?=$mnt?>"><?=$mnt?></option>
                                                <?php endforeach?>
                                            </select>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                            <canvas id="barChartOne"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 grid-margin stretch-card">
                    <div class="card">
                        <div class="card-body">
                            <nav class="navbar p-0" id="cht2">
                                <div class="d-flex">
                                    <a class="navbar-brand" href="#">
                                        <h4 class="card-title">Speditionen</h4>
                                    </a>
                                    <ul class="navbar-nav ml-auto">
                                        <li class="nav-item">
                                            <select class="form-select form-select-sm small"
                                                id="select-month-transporte">
                                                <option value="">wählen</option>
                                                <?php foreach($monthArray as $mnt):?>
                                                <option value="<?=$mnt?>"><?=$mnt?></option>
                                                <?php endforeach?>
                                            </select>
                                        </li>
                                    </ul>
                                </div>
                            </nav>
                            <canvas id="barChartTwo" height="1500"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <script src="vendor/base/vendor.bundle.base.js"></script>
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/statistik.js?t=<?=time()?>"></script>
</body>

</html>