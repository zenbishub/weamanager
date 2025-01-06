<?php

class Zollgut
{
    private $dbpath;
    public function __construct($werknummer, $path = null)
    {
        if (empty(session_id())) {
            session_start();
        }
        $this->dbpath = $this->dbpath = $path . "db/" . $werknummer;
        error_reporting(0);
    }
    private function readTraffic($pfad = "../")
    {

        $liteDB = new Sqlite($_SESSION['werknummer'], $pfad);
        $q = "SELECT * FROM traffic ORDER BY rfnum DESC";
        $results = $liteDB->sqliteSelect($q);
        foreach ($results as $result) {
            $array[] = json_decode($result['object'], true);
        }
        return $array;
    }
    private function updateTraffic($rfnum, $data, $pfad = "../")
    {
        if (!empty($rfnum)) {
            $entry = json_encode($data);
            $liteDB = new Sqlite($_SESSION['werknummer'], $pfad);
            $q = "UPDATE traffic SET object='" . $entry . "' WHERE rfnum=$rfnum";
            $liteDB->sqliteQuery($q);
        }
    }
    private function getZollOrderList($path = null)
    {
        $waitlist = [];

        $arrays = $this->readTraffic($path);
        if (empty($arrays)) {
            return [];
        }
        foreach ($arrays as $array) {
            if ($array['Zollgut'] == "JA" || $array['Zollgut'] == "CLEARING")
                $waitlist[] = $array;
        }
        return $waitlist;
    }
    private function getZollDoneOrderList($path = null)
    {
        $waitlist = [];

        $arrays = $this->readTraffic($path);
        if (empty($arrays)) {
            return [];
        }

        foreach ($arrays as $array) {
            if ($array['Zollgut'] == "PASSIERT")
                $waitlist[] = $array;
        }
        krsort($waitlist);
        return $waitlist;
    }
    private function showMessages($rfnum)
    {
        if (!file_exists("../" . $this->dbpath . "/conversation.json")) {
            return "file does not exists";
        }
        $readDB = file_get_contents("../" . $this->dbpath . "/conversation.json");
        $return = "";
        $filterMessages = [];
        $readJsonAsArray = json_decode($readDB, true);
        foreach ($readJsonAsArray as $message) {
            if ($message['rfnum'] == $rfnum) {
                $filterMessages[] = $message;
            }
        }
        if (!empty($filterMessages)) {
            $return .= '<span title="klicken um Nachricht zu lesen" class="text-primary pointer show-conversation font-large" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="true" alt="' . $rfnum . '"><i class="ti-bell"></i><span class="count"></span></span>';
            $return .= '<div class="dropdown-menu dropdown-menu-right navbar-dropdown p-3 conv-popup shadow" aria-labelledby="notificationDropdown" data-bs-popper="none">';
            foreach ($filterMessages as $message) {
                $return .= '<div class="p-2 pt-1 rounded mb-1 alert-success border">
                     <div class="small d-block mb-2 font-italic float-end"><i class="ti-timer"></i> ' . date("H:i", $message['sendtime']) . '</div>
                     <h6 class="font-weight-bold">' . $message['message-who'] . '</h6>
                     <p class="font-weight-normal">
                     ' . $message['message-text'] . '
                     </p>
                 </div>';
            }
            $return .= '</div>';
        }
        return $return;
    }
    public function getLinkToLieferDokumenten($wartenummer, $knznummer, $scanndirectory, $path = "../")
    {
        $folder = array_diff(scandir($path . $scanndirectory), array('..', '.'));
        foreach ($folder as $files) {
            //echo $files. "<br>". date("dmY")."_Nr".$wartenummer.".pdf";
            if ($files == date("dmY") . "_Nr" . $wartenummer . "&" . $knznummer . ".pdf") {
                return $files;
            }
            if ($files == date("dmY") . "_WN_" . $wartenummer . ".pdf") {
                return $files;
            }
        }
    }
    public function zollWarteListe($path = null)
    {
        $o = new Controller();
        $getOrderList = $this->getZollOrderList($path);
        foreach ($getOrderList as $array) {
            $Zollmeldung = null;
            $zollsendungen = null;
            $plombe = null;
            $lieferDokumente = null;
            if (!empty($array['Zollmeldung'])) {
                $Zollmeldung = '<tr><td>Zollmeldung</td><td>' . $o->modifyText($array['Zollmeldung']['Zollmeldung']) . '</td></tr>';
            }
            if (!empty($array['Zoll-Sendungen'])) {
                $zollsendungen =  '<tr><td>Sendung/Info</td><td>' . $array['Zoll-Sendungen']['Sendungen'] . ' / ' . $array['Zoll-Sendungen']['Collis'] . '</td></tr>';
            }
            if (!empty($array['Lieferschein'])) {
                $plombe = '<tr>
                    <td>Plombe</td> <td>
                    <a href="#" class="pictureviwever-show modal-xl controls"  alt="db/' . $_SESSION['werknummer'] . '/img_temp/' . $array['Lieferschein'] . '" data-bs-toggle="modal" data-bs-target="#pictureviwever">anzeigen</a>
                    </td>
                </tr>';
            }
            $lfDokumente = $this->getLinkToLieferDokumenten($array['rfnum'], $array['Nummer'], $o->scanFolder);
            if (!empty($lfDokumente)) {
                $lieferDokumente = '<tr>
                    <td>Dokumente</td><td>
                    <a href="javascript:window.open(\'' . $o->scanFolder . $lfDokumente . '?t=' . time() . '\',\'Lieferdokumenten\',\'width=1000 height=850\')">anzeigen</a>
                    </td>
                </tr>';
            }
            switch ($array['Zollmeldung']['Zollannahme']) {
                case "clearing":
                    $zollstatus = "clearing";
                    $statusBadge = "badge-info";
                    break;
                case "abgelehnt":
                    $zollstatus = "abgelehnt";
                    $statusBadge = "badge-danger";
                    break;
                default:
                    $zollstatus = "wartend";
                    $statusBadge = "badge-warning";
            }
            echo '<div class="card mb-2">
                <div class="card-header ps-2 pe-0">
                <div class="row">
                <div class="col-9 ps-0">
                    <span class="badge badge-info rounded">' . $array['rfnum'] . '</span>
                    <span class="h4">' . $array['Nummer'] . '</span> ';
            if (!empty($array['knznummer_aufleger'])) {
                echo ' / <span class="h4">' . $array['knznummer_aufleger'] . '</span>';
            }
            echo '<span class="ms-2"><img src="assets/img/flage' . $array['Sprache'] . '.JPG" class="img-fluid rounded me-1" style="min-width:20px; max-width:33px"></span>
                </div>
                <div class="col-1 p-0">';
            echo $this->showMessages($array['rfnum']);
            echo '</div>
                    <div class="col-1 text-right p-0 pe-1">
                    <span class="badge ' . $statusBadge . ' p-2  rounded">' . $zollstatus . '</span>
                    </div>
                    <div class="col-1 p-0 text-right">
                    <div class="dropdown no-arrow">
                    <a class="dropdown-toggle clear-interval-improzess font-large" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-primary"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" style="">
                        <div class="dropdown-header">Actions</div>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item info-erfassen" data="' . $array['rfnum'] . '"  data-bs-toggle="modal" data-bs-target="#zollactionModal">
                            <span class="me-2"><i class="ti-info"></i></span> Info erfassen
                        </a>
                        <a href="#" class="dropdown-item opensendbox" alt="' . $array['rfnum'] . '%20' . $array['Nummer'] . '%20' . $array['Firma'] . '%20' . $array['Werknummer'] . '" data-bs-toggle="modal" data-bs-target="#messageModal">
                            <span class="me-2"><i class="ti-location-arrow"></i></span> Nachricht an Fahrer
                        </a>
                        <a href="#" class="dropdown-item message-item" data="' . $_SESSION['weamanageruser'] . '&Zentrale" title="Nachricht senden" data-bs-toggle="modal" data-bs-target="#evochatModal">
                            <span class="me-2"><i class="ti-location-arrow"></i></span> Chat mit Interne-Empfänger
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="class/action.php?remove_from_order=' . $array['rfnum'] . '&rfnum=' . $array['rfnum'] . '&returnUri=zollgut" class="dropdown-item remove-from-order"><span class="me-2"><i class="ti-trash"></i></span> Löschen</a>
                    </div>
                    </div>
                </div>
                </div>
                </div>
                <div class="card-body overflow-auto pb-0">';
            echo "<div class='row'>";
            echo '<div class="col-11 p-1">';
            echo '<table class="table table-responsive  table-customized">';
            echo $Zollmeldung;
            echo '<tr><td>Spedition </td><td>' . $array['Firma'] . '</td></tr>';
            echo '<tr><td>Typ</td><td>' . $array['FRZTyp'] . '</td></tr>';
            echo '<tr><td>Ladung</td><td>' . $array['Ladung'] . ' <a href="#" data-index="' . $array['rfnum'] . '" class="float-end link change-unload-plant">Werk ändern</a></td></tr>';
            echo '<tr><td>Kennzeichen</td><td>' . $array['Nummer'] . '</td></tr>';
            echo '<tr><td>Anhänger</td><td>' . $array['knznummer_aufleger'] . '</td></tr>';
            echo '<tr><td>Anmeldung</td><td>' . $array['Anmeldung'] . '</td></tr>';
            echo $zollsendungen;
            echo $plombe;
            echo $lieferDokumente;
            echo '<tr><td>Scanner</td><td>' . $array['scanner'] . '<span class="ms-2"></span>
                                <button class="btn btn-sm p-1 ps-2 pe-2 btn-info float-end text-white checkScannerOnline" alt="' . substr($array['scanner'], -6) . '">check</button>
                            </td></tr>';
            echo '</table>';
            echo '</div>';
            echo '<div class="col-1 p-1 text-end">';
            echo '<button class="btn btn-info p-1 text-white more-infomation" data-index="' . $array['knznummer_aufleger'] . ':' . $array['Firma'] . '" data-bs-toggle="modal" data-bs-target="#cardInfo"><i class="ti-info"></i></button>';
            echo '</div>';
            echo "</div>";
            echo '</div>
                <div class="card-footer p-1">
                    <div class="row">
                    <div class="col-4 p-1 text-center">
                        <button type="button" class="col btn btn-success text-light zoll-abfetigung-btn zoll-information p-2  ps-1 pe-1" title="' . $array['Nummer'] . '" alt="' . $array['rfnum'] . '" data="1">Entladeerlaubnis<br>erteilt</button>
                    </div>
                    <div class="col-4 p-1 text-center">
                        <button class="col btn btn-warning text-light zoll-information p-2 ps-1 pe-1" data="2" alt="' . $array['rfnum'] . '" data-bs-toggle="modal" data-bs-target="#zollactionModal">Ware für<br>andere Werke</button>
                    </div>
                    <div class="col-4 p-1 text-center">
                        <button class="col btn btn-danger text-light zoll-information p-2 ps-1 pe-1" data="3" alt="' . $array['rfnum'] . '" data-bs-toggle="modal" data-bs-target="#zollactionModal">Bearbeitung<br>abgelehnt</button>
                    </div>
                    </div>
                </div>
            </div>';
        }
    }
    public function zollDoneListe($path = null)
    {
        $o = new Controller();
        $getOrderList = $this->getZollDoneOrderList($path);
        foreach ($getOrderList as $array) {
            $Zollmeldung = null;
            $zollsendungen = null;
            $plombe = null;
            $lieferDokumente = null;
            if (!empty($array['Zollmeldung'])) {
                $Zollmeldung = '<tr><td>Zollmeldung</td><td>' . $o->modifyText($array['Zollmeldung']['Zollmeldung']) . '</td></tr>';
            }
            if (!empty($array['Zoll-Sendungen'])) {
                $zollsendungen =  '<tr><td>Sendung/Info</td><td>' . $array['Zoll-Sendungen']['Sendungen'] . ' / ' . $array['Zoll-Sendungen']['Collis'] . '</td></tr>';
            }
            if (!empty($array['Lieferschein'])) {
                $plombe = '<tr>
                    <td>Plombe</td> <td>
                    <a href="#" class="pictureviwever-show modal-xl controls"  alt="db/' . $_SESSION['werknummer'] . '/img_temp/' . $array['Lieferschein'] . '" data-bs-toggle="modal" data-bs-target="#pictureviwever">anzeigen</a>
                    </td>
                </tr>';
            }
            $lfDokumente = $this->getLinkToLieferDokumenten($array['rfnum'], $array['Nummer'], $o->scanFolder);
            if (!empty($lfDokumente)) {
                $lieferDokumente = '<tr>
                    <td>Dokumente</td><td>
                    <a href="javascript:window.open(\'' . $o->scanFolder . $lfDokumente . '?t=' . time() . '\',\'Lieferdokumenten\',\'width=1000 height=850\')">anzeigen</a>
                    </td>
                </tr>';
            }
            switch ($array['Zollmeldung']['Zollannahme']) {
                case "clearing":
                    $zollstatus = "clearing";
                    $statusBadge = "badge-info";
                    break;
                default:
                    $zollstatus = $array['Zollgut'];
                    $statusBadge = "badge-success";
            }
            if (date("d.m.", $array['Zollabfertigung']) == date("d.m.")) {
                echo '<div class="card mb-2">
                <div class="card-header ps-2 pe-0">
                    <div class="row">
                        <div class="col-9 p-0">
                        <span class="badge badge-info rounded">' . $array['rfnum'] . '</span>
                            <span class="h4">' . $array['Nummer'] . '</span>';
                if (!empty($array['knznummer_aufleger'])) {
                    echo ' / <span class="h4">' . $array['knznummer_aufleger'] . '</span>';
                }
                echo '<span class="ms-2"><img src="assets/img/flage' . $array['Sprache'] . '.JPG" class="img-fluid rounded me-1" style="min-width:20px; max-width:33px"></span>
                        </div>';
                echo '<div class="col-1 p-0">';
                echo $this->showMessages($array['rfnum']);
                echo '</div>';
                echo '<div class="col-1 p-0">
                            <span></span>
                            <span class="float-end badge ' . $statusBadge . ' p-2  rounded">' . $zollstatus . '</span>
                        </div>
                            <div class="col-1 p-0 text-right">
                    <div class="dropdown no-arrow">
                    <a class="dropdown-toggle clear-interval-improzess font-large" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-primary"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" style="">
                        <div class="dropdown-header">Actions</div>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item info-erfassen" data="' . $array['rfnum'] . '"  data-bs-toggle="modal" data-bs-target="#zollactionModal">
                            <span class="me-2"><i class="ti-info"></i></span> Info erfassen
                        </a>
                        <a href="#" class="dropdown-item opensendbox" alt="' . $array['rfnum'] . '%20' . $array['Nummer'] . '%20' . $array['Firma'] . '%20' . $array['Werknummer'] . '" data-bs-toggle="modal" data-bs-target="#messageModal">
                            <span class="me-2"><i class="ti-location-arrow"></i></span> Nachricht an Fahrer
                        </a>
                        <a href="#" class="dropdown-item message-item" data="' . $_SESSION['weamanageruser'] . '&Zentrale" title="Nachricht senden" data-bs-toggle="modal" data-bs-target="#evochatModal">
                            <span class="me-2"><i class="ti-location-arrow"></i></span>  Chat mit Interne-Empfänger
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="class/action.php?remove_from_order=' . $array['rfnum'] . '&rfnum=' . $array['rfnum'] . '&returnUri=zollgut" class="dropdown-item remove-from-order"><span class="me-2"><i class="ti-trash"></i></span> Löschen</a>
                    </div>
                    </div>
                </div>
                    </div>
                </div>
                 <div class="card-body overflow-auto pb-0">';
                echo "<div class='row'>";
                echo '<div class="col-11 p-1">';
                echo '<table class="table table-customized">';
                echo $Zollmeldung;
                echo '<tr><td>Spedition</td><td>' . $array['Firma'] . '</td></tr>';
                echo '<tr><td>Typ</td><td>' . $array['FRZTyp'] . '</td></tr>';
                echo '<tr><td>Kennzeichen</td><td>' . $array['Nummer'] . '</td></tr>';
                echo '<tr><td>Anhänger</td><td>' . $array['knznummer_aufleger'] . '</td></tr>';
                echo '<tr><td>Ladung</td><td>' . $array['Ladung'] . ' <a href="#" data-index="' . $array['rfnum'] . '" class="float-end link change-unload-plant">Werk ändern</a></td></tr>';
                echo '<tr><td>Anmeldung</td><td>' . $array['Anmeldung'] . '</td></tr>';
                echo $zollsendungen;
                echo $plombe;
                echo $lieferDokumente;
                echo '<tr><td>Scanner</td><td>' . $array['scanner'] . '<span class="ms-2"></span>
                                <button class="btn btn-sm p-1 ps-2 pe-2 btn-info float-end text-white checkScannerOnline" alt="' . substr($array['scanner'], -6) . '">check</button>
                            </td></tr>';
                echo '</table>';
                echo '</div>';
                echo '<div class="col-1 p-1 text-end">';
                echo '<button class="btn btn-info p-1 text-white more-infomation" data-index="' . $array['knznummer_aufleger'] . ':' . $array['Firma'] . '" data-bs-toggle="modal" data-bs-target="#cardInfo"><i class="ti-info"></i></button>';
                echo '</div>
                        </div></div>
                <div class="card-footer">
                    <div class="row">
                    <div class="col-3 p-0 small">
                        Abfertigung
                    </div>
                    <div class="col-3 p-0 small">
                    <span class="p-2 font-large rounded">' . date("d.m. H:i", $array['Zollabfertigung']) . '</span>
                    </div>
                    <div class="col-6 p-0 text-end">';
                if (!empty($array['Drive-In'] && $array['Status'] == 50)) {
                    switch ($array['Drive-In']) {
                        case 1:
                            $driveIn = 'LKW fährt rein';
                            $styleDriveIn = "badge badge-success rounded text-white small";
                            break;
                        case 2:
                            $driveIn = "LKW fährt nicht rein";
                            $styleDriveIn = "badge badge-danger rounded text-white small";
                            break;
                    }
                    echo '<span class="' . $styleDriveIn . '">' . $driveIn . '</span>';
                }
                if ($array['Status'] == 75) {
                    echo '<span class="spinner-grow text-primary me-2" role="status" title="LKW-Fahrer steht auf Entladeposition"><span class="visually-hidden"></span></span>';
                }
                if ($array['Status'] == 80) {
                    echo '<span class="spinner-grow text-success me-2" role="status" title="Stapler hat Be/Entladeprozess gestartet"><span class="visually-hidden"></span></span>';
                }
                if ($array['Status'] == 100 || $array['Status'] == 120) {
                    $driveIn = "Werksgelände verlassen";
                    $styleDriveIn = "badge badge-warning rounded text-dark small";
                    echo '<span class="' . $styleDriveIn . '">' . $driveIn . '</span>';
                }
                echo '</div>
                    </div>
                 </div>
                </div>';
            }
        }
    }
    public function addZollNotice()
    {
        if (isset($_REQUEST['add_zoll_meldung'])) {

            $rfnum = $_REQUEST['rfnum'];
            $zollannahme = $_REQUEST['zollannahme'];
            $Zollmeldung = $_REQUEST['Zollmeldung'];
            $Zollbearbeitung = $_REQUEST['Zollbearbeitung'];

            $liteDB = new Sqlite($_SESSION['werknummer']);
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            $readJsonAsArray['Zollgut'] = "CLEARING";
            $readJsonAsArray['Zollmeldung'] = [
                "Zollannahme" => $zollannahme,
                "Grund" => $Zollbearbeitung,
                "Zollmeldung" => $Zollmeldung
            ];
            $readJsonAsArray['Zollabfertigung'] = time();
            $this->updateTraffic($rfnum, $readJsonAsArray, "../");
            header("location:../zollgut");
            exit;
        }
    }
    public function modal($id, $size)
    {
        echo '<div class="modal fade" id="' . $id . '" data-bs-backdrop="static" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog ' . $size . '">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="header-' . $id . '">Modal title</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body overflow-auto" id="body-' . $id . '"></div>
                </div>
            </div>
        </div>';
    }
}