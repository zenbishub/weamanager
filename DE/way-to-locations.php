<?php 
    require_once 'class/Controller.php';
    $o = new Controller();
    function getLocationsData(){
       $readDB = file_get_contents("db/".$_SESSION['werknummer']."/entladestellen.json");
       $arrays =  json_decode($readDB,true);
       ksort($arrays);
       return $arrays;
    }
    $arrays = getLocationsData();
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weg zu Abladestellen</title>
    <link rel="stylesheet" href="vendor/ti-icons/css/themify-icons.css">
    <link rel="stylesheet" href="vendor/base/vendor.bundle.base.css">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="vendor/jquery-ui/jquery-ui.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <link href="css/locations.css?t=<?=time()?>" rel="stylesheet">
    <link href="css/daimler.css" rel="stylesheet">
    <link rel="shortcut icon" href="../images/favicon.png" />
</head>

<body>
    <div class="container-fluid bg-dark">
        <div class="row p-2">
            <?php foreach($arrays as $array):
            if(!empty($array['Video'])):?>
            <div class="col-12 col-md-6 col-lg-4 p-2">
                <div class="card border-0">
                    <div class="card-header h5">
                        <?=$array['Platz']?>
                    </div>
                    <div class="card-body bg-dark p-0">
                        <video src="assets/vid/<?=$array['Video']?>" controls></video>
                    </div>
                </div>
            </div>
            <?php endif; endforeach;?>
        </div>
        <div class="row d-none justify-content-center" id="overlay-video-digit">
            <div class="col-12 col-lg-3">
                <input type="text" id="video-digit" placeholder="--">
            </div>
        </div>
    </div>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/base/vendor.bundle.base.js"></script>
    <script src="vendor/jquery-ui/jquery-ui.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/locations.js?s=<?=time()?>"></script>

</body>

</html>