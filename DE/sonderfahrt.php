<?php
spl_autoload_register(function ($class_name) {
    include 'class/'.$class_name . '.php';
});
$o =            new Controller();
$lieferant =    new Lieferant($o->scanFolder);
$sonderfahrt =  new Sonderfahrt($_SESSION['werknummer']);
$o->checkSession();
$sofah = $sonderfahrt->sonderfahrtenByDispoID();
$plants = $o->selectPlant();
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
    <link href="css/daimler.css" rel="stylesheet">
    <link rel="shortcut icon" href="images/favicon.png" />
</head>

<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark db-petrol20 fixed-top">
        <a class="navbar-brand ps-3" href="<?=$o->base?>sonderfahrt">EvoBus WEA | <span
                class="small"><?=$_SESSION['werkname']."(".$_SESSION['werknummer'].")"?>
        </a>
        <ul class="navbar-nav ms-auto me-0 me-md-3 my-2 my-md-0">
            <li class="nav-item dropdown">
                <a class="nav-link text-white dropdown-toggle" id="navbarDropdown" href="#" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="class/action.php?logout=1&appname=sonderfahrt"><i
                                class="ti-power-off"></i> Logout</a></li>
                </ul>
            </li>
        </ul>
    </nav>
    <div class="p-1" id="showhidenavi"></div>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4 px-md-3 px-lg-2 pt-2">
                <div class="card p-1">
                    <div class="card-header p-2 mb-2">
                        Sonderfahrt im Wareneingeng anmelden
                    </div>
                    <div class="card-body p-0" id="main-viewport">
                        <div class="row justify-content-center mt-2" id="workspace">
                            <div class="col-md-4 grid-margin stretch-card">
                                <div class="card">
                                    <div class="card-body">
                                        <form class="forms-sample" id="form-add-sonerfahrt" action="class/action.php"
                                            method="POST">
                                            <h4 class="card-title">Anmelder</h4>
                                            <div class="form-group row mb-1">
                                                <label for="dispo-name" class="col-sm-3 col-form-label p-1">Disponent
                                                    *</label>
                                                <div class="col-sm-9 p-1">
                                                    <input type="text" class="form-control " id="dispo-name"
                                                        name="Dispo_Name" placeholder="Name" required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="dispo-email" class="col-sm-3 col-form-label p-1">E-mail
                                                    *</label>
                                                <div class="col-sm-9 p-1">
                                                    <input type="email" class="form-control" id="dispo-email"
                                                        name="Dispo_Email" placeholder="E-mail" required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-5">
                                                <label for="dispo-telefon" class="col-sm-3 p-1 col-form-label">Telefon
                                                    *</label>
                                                <div class="col-sm-9 p-1">
                                                    <input type="text" class="form-control" id="dispo-telefon"
                                                        name="Dispo_Telefon" placeholder="Telefonnummer" required>
                                                </div>
                                            </div>
                                            <h4 class="card-title">Lieferung</h4>
                                            <div class="form-group row mb-1">
                                                <label for="exampleInputPassword2"
                                                    class="col-sm-3 p-1 col-form-label">Priorität</label>
                                                <div class="col-4">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="radio" class="form-check-input" name="Prio"
                                                                value="Dringend" required>
                                                            Dringend
                                                            <i class="input-helper"></i></label>
                                                    </div>
                                                </div>
                                                <div class="col-5">
                                                    <div class="form-check">
                                                        <label class="form-check-label">
                                                            <input type="radio" class="form-check-input" name="Prio"
                                                                value="Sehr dringend" required>
                                                            Sehr dringend
                                                            <i class="input-helper"></i></label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="lieferwerk" class="col-sm-3 col-form-label p-1">lieferwerk
                                                    *</label>
                                                <div class="col-sm-6 p-1">
                                                    <select class="form-control" id="Lieferwerk" name="Werkname"
                                                        required>
                                                        <option value="">wählen</option>
                                                        <?php foreach($plants as $plant):?>
                                                        <option value="<?=$plant['Werkname']?>"><?=$plant['Werkname']?>
                                                        </option>
                                                        <?php endforeach;?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="datepicker" class="col-sm-3 col-form-label p-1">Lieferdatum
                                                    *</label>
                                                <div class="col-sm-6 p-1">
                                                    <input type="text" class="form-control" autocomplete="off"
                                                        id="datepicker" name="Lieferdatum" placeholder="Datum" required>
                                                </div>
                                                <div class="col-3 p-1">
                                                    <select class="form-control" name="Zeitfenster" required>
                                                        <option value="">Uhrzeit</option>
                                                        <?php $i=6; do{?>
                                                        <option value="<?=$i?>:00"><?=$i?>:00</option>
                                                        <?php $i++;}while($i<23);?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="Spedition" class="col-sm-3 col-form-label p-1">Spedition
                                                    *</label>
                                                <div class="col-sm-9 p-1">
                                                    <input type="telephone" class="form-control " id="Spedition"
                                                        name="Firma" placeholder="Transportfirma / Spedition" required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="Lieferant" class="col-sm-3 col-form-label p-1">Lieferant
                                                    *</label>
                                                <div class="col-sm-9 p-1">
                                                    <input type="text" class="form-control " id="Lieferant"
                                                        name="Lieferant" placeholder="Lieferantenname" required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="Mat-Nummer" class="col-sm-3 col-form-label p-1">Mat.
                                                    Nummer</label>
                                                <div class="col-sm-9 p-1">
                                                    <input type="text" class="form-control " id="Mat-Nummer"
                                                        name="Materialnummer" placeholder="Materialnummer">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="BB-Nummer"
                                                    class="col-sm-3 col-form-label p-1">BB-Nummer</label>
                                                <div class="col-sm-9 p-1">
                                                    <input type="text" class="form-control " id="BB-Nummer"
                                                        name="BB_Nummer" placeholder="BB-Nummer">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="Arbeitstelle" class="col-sm-3 col-form-label p-1">PVB /
                                                    Arbeitstelle</label>
                                                <div class="col-sm-9 p-1">
                                                    <input type="text" class="form-control " id="Arbeitstelle"
                                                        name="Arbeitstelle" placeholder="Arbeitstelle">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <label for="Bemerkungen"
                                                    class="col-sm-3 col-form-label p-1">Bemerkungen</label>
                                                <div class="col-sm-9 p-1">
                                                    <textarea class="form-control " id="Bemerkungen" name="Bemerkungen"
                                                        placeholder="..."></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row mt-3 mb-3 small">
                                                <div class="col-12 small">
                                                    * Pflichfelder müssen ausgefüllt werden
                                                </div>
                                            </div>
                                            <div class="form-group row mb-1">
                                                <div class="col-12 p-1 text-end">
                                                    <input type="hidden" name="add_sonderfahrt" value="1">
                                                    <input type="hidden" name="reutrnURI" value="sonderfahrt">
                                                    <button type="submit" class="btn btn-primary me-2">erfassen</button>

                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 grid-margin pt-3 bg-light border">
                                <div class="row ">
                                    <?php foreach($sofah as $arrays):
                                      unset($arrays['rfnum']);
                                      unset($arrays['Name Fahrer']);
                                      unset($arrays['Legitimation']);
                                      unset($arrays['anmeldeID']);
                                      unset($arrays['FRZTyp']);
                                      unset($arrays['Nummer']);
                                      unset($arrays['Ladung']);
                                      unset($arrays['Gefahrgutpunkte']);
                                      unset($arrays['Zollgut']);
                                      unset($arrays['kennzeichnugspflichtig']);
                                      unset($arrays['Lieferschein']);
                                      unset($arrays['Beladen für']);
                                      unset($arrays['Sprache']);
                                      unset($arrays['ladung_beschreibung']);
                                      unset($arrays['Entladen']);
                                      unset($arrays['Entladung']);
                                      unset($arrays['Anmeldung']);
                                      unset($arrays['timestamp']);
                                      unset($arrays['Platz']);
                                      unset($arrays['Status']);
                                      unset($arrays['Protokoll_WA']);
                                      unset($arrays['Protokoll_VERS']);
                                      unset($arrays['Abfertigung']);
                                      unset($arrays['gone']);
                                      unset($arrays['alarm']);
                                      unset($arrays['WA_Buro']);
                                      unset($arrays['Lief_tstamp']);
                                      $arrays['erstellt'] = date("d.m.y H:i",$arrays['erstellt']);
                                      ?>
                                    <div class="card mb-2">
                                        <div class="card-body">
                                            <span class="float-end font-italic smaller">
                                                <?=$arrays['erstellt']?>
                                            </span>
                                            <h5 class="card-title">Sofah daten</h5>
                                            <table class="table table-striped table-responsive">
                                                <?php foreach($arrays as $key=>$value):?>
                                                <tr class="p-1">
                                                    <td><?=ucfirst($key)?></td>
                                                    <td><?=$value?></td>
                                                </tr>
                                                <?php endforeach;?>
                                            </table>
                                        </div>
                                    </div>
                                    <?php endforeach;?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="py-1 bg-light mt-auto fixed-bottom border-top">
            <div class="container-fluid px-1">
                <div class="float-end small">
                    <span class="text-muted small ">Copyright &copy;</span>
                </div>
            </div>
        </footer>
    </div>
    </div>
    <?php include 'content/modal.php'?>
    <?php include 'content/messagemodal.php'?>
    <?php include 'content/setschippermodal.php'?>
    <?php include 'content/imagemodal.php'?>
    <?php $o->alertModal();?>
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/base/vendor.bundle.base.js"></script>
    <script src="vendor/jquery-ui/jquery-ui.js"></script>
    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/demo/datatables-demo.js"></script>
    <script src="js/sb-admin-2.min.js"></script>
    <script src="js/off-canvas.js"></script>
    <script src="js/scripts.js"></script>
    <script src="js/hoverable-collapse.js"></script>
    <script src="js/todolist.js"></script>
    <script src="js/sonderfahrt.js?s=<?=time()?>"></script>
</body>

</html>