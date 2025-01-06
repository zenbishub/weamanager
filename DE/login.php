<?php
$app=null;
extract($_REQUEST);
require_once 'class/Controller.php';
$o = new Controller();
$werkdata = $o->selectPlant();
$qrCodeButton = null;

switch($app){ 
  case "monitor":
    $appTitle = "MONITOR";
    break;
  case "maingate":
    $appTitle = "PFORTE";
    break;
  case "stapler":
    if(isset($_SESSION['weamanageruser'])){
        header("location:$app");
        exit;
    }
    $appTitle = "STAPLER";
    $qrCodeButton='<div class="float-end text-center">
    <button type="button" class="btn btn-secondary btn-rounded btn-icon d-block" alt="'.$routefolder.'"
id="open-qrcode-reader" data-bs-toggle="modal"
data-bs-target="#readQRModal">
<i class="fas fa-qrcode"></i></button>
QRC
</div>';
break;
case "sonderfahrt":
$appTitle = "SONDERFAHRT";
break;
case "zollgut":
$appTitle = "ZOLLGUT";
break;
default :
$appTitle = "Steuermodul";
break;
case "information":
$appTitle = "INFORMATION";
break;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Anmelden</title>
    <link rel="stylesheet" href="vendor/ti-icons/css/themify-icons.css">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="vendor/base/vendor.bundle.base.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/custom.css?t=<?=time()?>">
    <link rel="shortcut icon" href="../images/favicon.png" />
</head>

<body>
    <div class="container-scroller">
        <div class="container-fluid page-body-wrapper full-page-wrapper">
            <div class="content-wrapper d-flex align-items-center auth px-0 body-image">
                <div class="row w-100 mx-0">
                    <div class="col-10 col-md-6 col-lg-5 col-xl-4 rounded-2 mx-auto">
                        <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                            <h4 class="float-right">Anmelden</h4>
                            <h6 class="mt-2 font-weight-light">TMI Manager / <?=$appTitle?></h6>

                            <form class="pt-3" action="class/action.php" method="post">
                                <div class="form-group">
                                    <select name="werknummer" class="form-control form-control-lg bg-light text-black"
                                        required>
                                        <option value="">Werk auswählen</option>
                                        <?php foreach($werkdata as $data):?>
                                        <option value="<?=$data['Werknummer']?>:<?=$data['INUMMER']?>">
                                            <?=$data['Werkname']?></option>
                                        <?php endforeach;?>
                                    </select>
                                </div>
                                <?php if($app=="weamanager"):?>
                                <div class="form-group">
                                    <select name="Abteilung" class="form-control form-control-lg bg-light text-black"
                                        required>
                                        <option value="">Abteilung</option>
                                        <?php foreach($werkdata as $data):
                                            foreach($data['Deparments'] as $key=>$Deparments):?>
                                        <option value="<?=$Deparments['Abt-ID']?>"><?=$Deparments['Abteilung']?>
                                        </option>
                                        <?php endforeach; endforeach;?>
                                    </select>
                                </div>
                                <?php endif;?>
                                <div class="form-group">
                                    <input type="search" name="user" class="form-control form-control-lg text-black"
                                        id="user" placeholder="Daimlertruck-ID" required>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="pass" class="form-control form-control-lg" id="pass"
                                        placeholder="Passwort" required>
                                </div>
                                <div class="form-group">
                                    <input type="hidden" name="fixpass" id="fixpass" placeholder="Ressource-Key"
                                        value="rtool">
                                    <input type="hidden" name="userlogin" value="1">
                                    <input type="hidden" name="app" value="<?=$app?>">
                                </div>
                                <div class="mt-3">
                                    <button type="submit"
                                        class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn text-light">login</button>
                                    <?=$qrCodeButton?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php 
  if(isset($_REQUEST['error'])):
    switch($_REQUEST['error']):
      case "ressourse_notfound":
        $o->Alert("error","Ressourse nicht gefunden.");
      break;
      case "user_notfound":
        $o->Alert("error","Benutzer nicht gefunden.");
      break;
      endswitch;
  endif;
    ?>
    <div class="modal fade" id="readQRModal" tabindex="-1" aria-labelledby="readQRModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="readQRModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="readQRModal-body"></div>
            </div>
        </div>
    </div>
    <script src="vendor/base/vendor.bundle.base.js"></script>
    <script src="js/login.js?t=<?=time()?>"></script>
</body>

</html>