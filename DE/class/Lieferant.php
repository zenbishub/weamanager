<?php

class Lieferant
{
    private $scanFolder;
    public $LKWfilter;

    public function __construct($scanDirectory = null)
    {
        $this->scanFolder = $scanDirectory;
        $this->LKWfilter = [
            "WE5" => "Wareneingang Werk 5",
            "WE9" => "Transport für Werk 9",
            "VERS5" => "Versand Werk 5"
        ];
    }
    private function getLocations()
    {
        $readDB = file_get_contents("../db/" . $_SESSION['werknummer'] . "/entladestellen.json");
        return json_decode($readDB, true);
    }
    private function validateProtokoll($protokoll)
    {
        $newValue = [];
        foreach ($protokoll as $key => $value) {
            if (substr($key, 0, 5) != "check") {
                $newValue[] = $value;
            }
        }
        $newCheck = [];
        foreach ($protokoll as $key => $value) {
            if (substr($key, 0, 5) == "check") {
                $newCheck[] = $value;
            }
        }
        $array = [];
        for ($i = 0; $i < count($newCheck); $i++) {
            if ($newCheck[$i] == "nein") {
                $array[] = $newValue[$i];
            }
        }
        return count($array);
    }
    private function showMessages($rfnum)
    {
        $readDB = file_get_contents("../db/" . $_SESSION['werknummer'] . "/conversation.json");
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
                    <h6 class="font-weight-bold">' . $message['addnewmessage'] . ' ' . $message['message-who'] . '</h6>
                    <p class="font-weight-normal">
                    ' . $message['message-text'] . '
                    </p>
                </div>';
            }
            $return .= '</div>';
        }
        return $return;
    }
    public function getLinkToLieferDokumenten($wartenummer, $knznummer, $path = "../")
    {
        $folder = array_diff(scandir($path . $this->scanFolder), array('..', '.'));
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
    private function checkPrio($prio)
    {
        if (empty($prio)) {
            return "";
        }
        switch ($prio) {
            case "D":
                return ["badge-warning", "Dringend"];
            case "SD":
                return ["badge-danger", "Sehr dringend"];
        }
    }
    private function geoPosition($scanner)
    {
        require_once 'Sqlite.php';
        $sqlite = new Sqlite($_SESSION['werknummer']);
        $q = "SELECT latitude,longtitude FROM scanner WHERE IP LIKE '%$scanner'";
        $result = $sqlite->sqliteSelect($q);
        return $result;
    }
    private function laufCard($array, $showStapler, $wartenummer, $knznummer)
    {

        $protokol_WA = json_decode($array['Protokoll_WA']);
        $protokol_Buro = json_decode($array['WA_Buro']);
        $reklamation = $array['Reklamation'];
        $zollmeldung = "";
        $MA_Auftrag = "";
        $borderLeft = "";
        $leergutmitnahme = $array['leergut_mitnahme'];

        if (!empty($array['MA-Autfrag'])) {
            $MA_Auftrag = '<tr class="alert alert-warning">
                <td class="small font-italic">Zusatz Auftrag: </td>
                <td>' . $array['MA-Autfrag']['umfang'] . '<br>' . $array['MA-Autfrag']['stapler_for_auftrag'] . '</td>
            </tr>';
        }
        switch ($array['Status']) {
            case null:
            case 25:
            case 50:
                $borderLeft = "border-left-3 border-left-info";
                break;
            case 75:
                $borderLeft = "border-left-3 border-left-success";
                break;
            case 100:
                $borderLeft = "border-left-3 border-left-warning";
                break;
        }
        $lieferDokumente = $this->getLinkToLieferDokumenten($wartenummer, $knznummer);
        $scanner = str_replace("Nr.: ", "", $array['scanner']);
        $position = $this->geoPosition($scanner);
        $content = '<div class="row">
            <div class="col-11 pb-2 bg-light overflow-auto ' . $borderLeft . '">
                <table class="table pt-2">';
        if (!empty($array['customized_time'])) {
            $content .= '
                <tr class="alert alert-info">
                    <td class="small font-italic">Einfahrt eingestellt:</td>
                    <td>' . $array['customized_time'] . '</td>
                </tr>';
        }
        if (!empty($array['Lieferschein'])) {
            $content .= '
                <tr>
                    <td class="small font-italic align-middle">LS / ZollPlombe:</td>
                    <td>
                        <a href="#" class="pictureviwever-show modal-xl controls btn btn-primary p-1" data="' . $array['Stapler']['BMI-Nummer'] . '&' . $_SESSION['weamanageruser'] . '" alt="db/' . $_SESSION['werknummer'] . '/img_temp/' . $array['Lieferschein'] . '" data-bs-toggle="modal" data-bs-target="#pictureviwever">anzeigen</a>
                    </td>
                </tr>';
        }
        if (!empty($lieferDokumente)) {
            $content .= '
                <tr>
                    <td class="small font-italic">LS-Scan</td><td>
                        <a href="javascript:window.open(\'' . $this->scanFolder . $lieferDokumente . '?t=' . time() . '\',\'Lieferdokumenten\',\'width=1000 height=850\')">LieferDoku</a>
                    </td>
                </tr>';
        }
        if (!empty($array['Zollmeldung'])) {
            $zollmeldung = "<br>" . $array['Zollmeldung']['Grund'] . " " . $array['Zollmeldung']['Zollmeldung'];
        }
        if (is_object(json_decode($array['leergut_mitnahme']))) {
            $lgmtn = json_decode($array['leergut_mitnahme'], true);
            $leergutmitnahme = $lgmtn['Leergut-LT'] . '/' . $lgmtn['Leergut-Mng'] . ' St.=>' . $lgmtn['set_stapler_leergut_unload'];
        }
        $content .= '
                <tr>
                    <td class="small font-italic">Spedition: </td> <td class="pl-2">' . $array['Firma'] . ' / ' . $array['Nummer'] . '</td> 
                </tr>
                <tr>
                    <td class="small font-italic">Ladung: </td> <td class="pl-2">' . $array['Ladung'] . '</td>
                </tr>';
        if (!empty($array['ladung_beschreibung'])) {
            $content .= '
                <tr>
                    <td class="small font-italic">Beschreibung</td> <td class="pl-2">' . $array['ladung_beschreibung'] . '</td>
                </tr>';
        }
        $content .= '<tr>
                    <td class="small font-italic">LGA Nummer: </td> <td class="pl-2">' . $array['leegut_abholnummer'] . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">Leergutmitnahme: </td> <td class="pl-2">' . $leergutmitnahme . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">Platz: </td> <td class="pl-2">' . $array['Platz'] . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">Fahrer: </td> <td>' . $array['Name Fahrer'] . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">Stapler: </td> ' . $showStapler . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">Zusatz Auftrag: </td><td>' . $MA_Auftrag . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">K-Num :</td> <td class="pl-2">' . $array['Nummer'] . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">Typ: </td> <td class="pl-2">' . $array['FRZTyp'] . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">Anhänger: </td> <td class="pl-2">' . $array['knznummer_aufleger'] . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">Beladen für</td> <td class="pl-2">' . $array['Beladen für'] . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">Entladen</td> <td class="pl-2">' . $array['Entladen'] . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">Zollgut</td> <td class="pl-2">' . $array['Zollgut'] . ' ' . $zollmeldung . '</td>
                </tr>';
        if (!empty($array['Zoll-Sendungen'])) {
            $content .= '<tr>
                    <td class="small font-italic">Zoll-Info</td><td>' . $array['Zoll-Sendungen']['Sendungen'] . ' / ' . $array['Zoll-Sendungen']['Collis'] . '</td>
                </tr>';
        }
        if (!empty($array['Gefahrgut'])) {
            $content .= '
                    <tr>
                        <td class="small font-italic">Gefahrgut</td> <td class="pl-2">' . $array['Gefahrgut'] . '/' . $array['Gefahrgutpunkte'] . ' Punkte</td>
                    </tr>';
        }
        if (!empty($array['Weiterleitung_von'])) {
            $content .= '<tr>
                        <td class="small-viewport">WL von</td><td class="ps-2">' . $array['Weiterleitung_von'] . '</td>
                    </tr>';
        }
        $content .= '
                <tr>
                    <td class="small font-italic">Ankunft: </td> <td class="pl-2">' . $array['Anmeldung'] . '</td> 
                </tr>
                <tr>
                    <td class="small font-italic">Eingesteuert</td><td class="pl-2">' . $array['eingesteuert'] . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">Einfahrt</td><td class="pl-2">' . $array['Einfahrt'] . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">Abfertigung</td><td class="pl-2">' . $array['Abfertigung'] . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">Quittiert</td><td class="pl-2">' . $protokol_WA->signed . '</td>
                </tr>
                <tr>
                    <td class="small font-italic">W-Eingang</td><td class="pl-2">' . $protokol_Buro->signed . '</td>
                </tr>';
        if (!empty($array['scanner'])) {
            $btnCallByClick = "btn-info";
            if ($array['alarm'] == "callByClick") {
                $btnCallByClick = "btn-success";
            }
            $content .= '<tr>
                        <td class="small font-italic">Scanner</td><td class="pl-2">
                            <div class="row">
                                <div class="col-5 p-0">
                                ' . $array['scanner'] . '
                                </div>
                                <div class="col-7 p-0">
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <span class="me-1"></span>
                                        <button class="btn btn-sm p-1 btn-info text-white checkScannerOnline me-1" alt="' . $scanner . '">check</button>
                                        <button class="btn btn-sm p-1 btn-info text-light open-map-iframe me-1" data="https://maps.google.com/maps?q=' . $position[0]['latitude'] . ',' . $position[0]['longtitude'] . '&z=17&output=embed&t=k&iwloc=addr">Karte</button>
                                        <button class="btn btn-sm ' . $btnCallByClick . ' p-1 text-white float-end me-1 call-once-by-click" data-index="' . $array['rfnum'] . '"><i class="ti-bell small"></i></button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>';
        }
        if (!empty($reklamation)) {
            $content .= '<tr><td colspan="2"><h5>Reklamation</h5>';
            $content .= '<div class="row mt-3">';
            $content .= '<div class="col-12 p-0 mb-2">';
            $content .= $reklamation['reklamation_beschreibung'];
            $content .= '</div>';
            foreach ($reklamation['reklamation_bilder'] as $bilder) {
                $content .= '<div class="col-3 p-0"><img data-bs-toggle="modal" title="' . $reklamation['reklamation_beschreibung'] . '" data-bs-target="#pictureviwever" data="&' . $_SESSION['weamanageruser'] . '" alt="db/' . $_SESSION['werknummer'] . '/reklamation/' . $bilder . '" class="pointer img-thumbnail pictureviwever-show" src="db/' . $_SESSION['werknummer'] . '/reklamation/TN' . $bilder . '"></div>';
            }
            $content .= '</div>';
            $content .= '</td><tr>';
        }
        $content .= '</table>
        </div>
        <div class="col-1 p-1  pt-2 alert-info text-center">';
        $content .= '<span class="pointer btn btn-info text-light border p-1 mb-2 card-infomation-frame" data="' . $array['rfnum'] . '" data-bs-toggle="modal" data-bs-target="#cardInfo"><i class="ti-info"></i></span>';
        $content .= '<span class="pointer btn btn-light border p-1 show-more-infomation"><i class="ti-arrows-vertical"></i></span>';
        $content .= '</div></div>';
        return $content;
    }
    public function warteschlange($getOrderList)
    {

        $check = null;
        $locations = $this->getLocations();
        foreach ($getOrderList as $array):

            $sonderfahrt = "bg-light";
            $textSofsha = "text-primary";
            $showStapler = "<td></td>";
            $marker = "";
            $stylePrio = $this->checkPrio($array['Prio']);
            if (!empty($array['Stapler'])) {
                $showStapler = '<td class="pl-2">
                 <a class="pictureviwever-show pointer link p-0" data="' . $array['Stapler']['BMI-Nummer'] . '"
                 title="' . $array['Stapler']['BMI-Typ'] . ' ' . $array['Stapler']['Hersteller'] . ' ' . $array['Stapler']['BMI-Nummer'] . '" alt="db/' . $_SESSION['werknummer'] . "/bmi/" . $array['Stapler']['BMI-Bild'] . '"
                 data-bs-toggle="modal" data-bs-target="#pictureviwever" alt="db/' . $_SESSION['werknummer'] . "/bmi/" . $array['Stapler']['BMI-Bild'] . '" src="db/' . $_SESSION['werknummer'] . "/bmi/TN" . $array['Stapler']['BMI-Bild'] . '"> ' . $array['Stapler']['Hersteller'] . ' ' . $array['Stapler']['BMI-Nummer'] . '
                  <i class="ti-gallery"></i></a>
                  </td>';
            }

            switch ($array['Status']):
                case null:
                case 25:
                    $classRequire = "bg-light";
                    $footer = "";
                    break;
                case 50:
                    $classRequire = "alert-warning";
                    $footer = '<div class="card-footer p-2">
                    <form method="POST" action="class/action.php">
                        <input type="hidden" name="return_uri" id="return_uri" value="nonepublic">
                        <input type="hidden" name="Status" value="75">
                        <input type="hidden" name="entry_passed" id="entry_passed" value="' . $array['rfnum'] . '">
                        <button type="sub" class="btn btn-primary pass-througt-entry p-2 text-light border-light">Entladeposition erreicht</button>
                    </form>
                </div>';
                    break;
                default:
                    $classRequire = "bg-light";
                    $footer = "";
                    break;
            endswitch;
            if (!empty($array['sofahnum'])) {
                $sonderfahrt = "bg-danger";
                $textSofsha = "text-white";
            }
            if ($array['Ladung'] == "Versand Werk 5") {
                $marker = '<span class="badge badge-info me-1  rounded">Vers-W5</span>';
            }
            if ($array['Ladung'] == "Transport für Werk 9") {
                $marker = '<span class="badge badge-info me-1  rounded">Werk 9</span>';
            }
            if (!empty($array['LegitimationConfirm'])):
                if ($array['Status'] != 501 && $array['Status'] != 50 && $array['Status'] != 75 && $array['Status'] != 80 && $array['Status'] != 100 && $array['Status'] != 120):
                    $check++;
                    echo '<div class="col-12 p-0 card-info card-order card-drag mb-2" alt="' . $array['rfnum'] . '">
                <div class="card ' . $classRequire . ' shadow rounded" title="Laufnummer ' . $array['rfnum'] . '">';
                    if ($_SESSION['weamanager_roll'] != "PforteUser") {
                        echo '<div class="card-header p-2 ' . $sonderfahrt . ' card-head-hover">
                        <div class="row">
                            <div class="col-4 p-0 border-left">
                                <form method="POST" action="class/action.php">
                                <select name="Platz" class="p-1 col-10 form-control-sm text-dark small d-inline onclick-clear-interval setplace-select" required>
                                    <option value="">Entladeplatz</option>';
                        foreach ($locations as $location) {
                            echo '<option value="' . $location['Platz'] . '">' . $location['Platz'] . '</option>';
                        }
                        echo '</select>
                                <input type="hidden" name="add_to_prozess" value="1">
                                <input type="hidden" name="current_status" value="' . $array['Status'] . '">
                                <input type="hidden" name="rfnum" value="' . $array['rfnum'] . '">
                               
                            </form>
                            </div>';
                        echo '<div class="col-7 pe-0 border-left">';
                        echo '<span class="badge badge-info rounded me-1 cursor-default">' . $array['rfnum'] . '</span>';
                        echo $marker;
                        if ($array['Ladung'] == "Paketdienst") {
                            echo "<div class='badge badge-info rounded me-1 pointer' title='Achtung Paketdienst!'><i class='ti-alert'></i> PD</div>";
                        }
                        if ($array['leergut_mitnahme'] == "JA") {
                            echo "<div class='badge badge-info rounded me-1 pointer' title='Achtung Leergutmitnahme!'><i class='ti-alert'></i> LGM</div>";
                        }
                        if (!empty($array['Prio'])) {
                            echo '<span class="badge rounded me-1 cursor-default ' . $stylePrio[0] . '" title="' . $stylePrio[1] . ' ' . $array['Prio-Melder'] . '">' . $array['Prio'] . '</span>';
                        }
                        if ($array['Zollgut'] == "JA") {
                            echo '<span class="badge badge-danger rounded me-1 cursor-default" title="Warten auf Zollabfertigung">Zoll</span>';
                        }
                        if ($array['Zollgut'] == "CLEARING") {
                            echo '<span class="badge badge-warning rounded me-1 cursor-default" title="' . $array['Zollmeldung']['Zollmeldung'] . '">Zoll</span>';
                        }
                        if ($array['Zollgut'] == "PASSIERT") {
                            echo '<span class="badge badge-success rounded me-1 cursor-default" title="Entladeerlaubnis erteilt">Zoll</span>';
                        }
                        if ($array['Gefahrgut'] == "JA") {
                            echo '<span class="badge badge-danger rounded me-1 cursor-default" title="Gefahrgut: ' . $array['Gefahrgut'] . '/' . $array['Gefahrgutpunkte'] . ' Punkte">GG</span>';
                        }
                        echo '<img src="assets/img/flage' . $array['Sprache'] . '.JPG" class="img-fluid rounded me-1 cursor-default" style="width:25px" title="' . $array['Sprache'] . '">';
                        echo $this->showMessages($array['rfnum']);

                        switch ($array['Drive-In']) {
                            case 1:
                                $driveIn = "LKW fährt rein";
                                $styleDriveIn = "class='badge badge-success rounded text-white smaller'";
                                break;
                            case 2:
                                $driveIn = "LKW fährt nicht rein";
                                $styleDriveIn = "class='badge badge-danger rounded text-white smaller' ";
                                break;
                            default:
                                $driveIn = "";
                                $styleDriveIn = "";
                        }

                        echo '<div ' . $styleDriveIn . '  alt="' . $array['rfnum'] . '">' . $driveIn . '</div>';
                        echo '</div>
                            <div class="col-1 text-end p-0">
                                    <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle onclick-clear-interval font-large" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw ' . $textSofsha . '"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right z-index-high shadow animated--fade-in" style="">
                                        <div class="dropdown-header">Actions</div>
                                        <a class="dropdown-item opensendbox" data-bs-toggle="modal"
                                        alt="' . $array['rfnum'] . '%20' . $array['Nummer'] . '%20' . $array['Firma'] . '%20' . $array['Werknummer'] . '" data-bs-target="#messageModal" data-bs-whatever="@getbootstrap" href="#"><span class="me-2"><i class="ti-location-arrow"></i></span> Nachricht senden</a>
                                        <a href="#" class="dropdown-item set-unload-place" alt="' . $array['rfnum'] . '"><span class="me-2"><i class="ti-target"></i></span>Abladeplatz zuweisen</a>
                                        <a href="#" alt="' . $array['rfnum'] . ':' . $array['Nummer'] . '" class="dropdown-item set-forkbully" data-bs-toggle="modal" data-bs-target="#setSchipperModal"> <span class="me-2"><i class="ti-target"></i></span> Stapler zuweisen</a>
                                        <a href="#" alt="' . $array['rfnum'] . ':' . $array['Nummer'] . '" class="dropdown-item set-incomming-time" data-bs-toggle="modal" data-bs-target="#setIncomminTime"> <span class="me-2"><i class="ti-target"></i></span> Einfahrtzeit hinzufügen</a>
                                        <div class="dropdown-divider"></div>
                                        <a href="class/action.php?rfnum=' . $array['rfnum'] . '" class="dropdown-item remove-from-order"><span class="me-2"><i class="ti-trash"></i></span> Löschen</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>';
                    }
                    echo '<div class="card-body p-0 rounded card-height-small">
                            ' . $this->laufCard($array, $showStapler, $array['rfnum'], $array['Nummer']) . '
                        </div>';
                    if ($_SESSION['weamanager_roll'] != "PforteUser") {
                        echo $footer;
                    }
                    echo '</div>
                </div>';
                endif;
            endif;
        endforeach;
        if (empty($check)) {
            echo "<center><span class='small'>keine Daten</span></center>";
        }
    }
    public function imProzess($getOrderList)
    {
        $check = null;
        foreach ($getOrderList as $array):
            $sonderfahrt = "";
            $textSofsha = "text-primary";
            $showStapler = "<td></td>";
            $stylePrio = $this->checkPrio($array['Prio']);

            $treepoints = '<div class="col-1 text-end p-0">
                            <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle clear-interval-improzess font-large" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw ' . $textSofsha . '"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" style="">
                                        <div class="dropdown-header">Actions</div>
                                        <a href="class/action.php?back_to_waitlist=1&rfnum=' . $array['rfnum'] . '" class="dropdown-item"><span class="me-2">
                                            <i class="ti-arrow-left"></i></span> zu Warteschlange
                                        </a>
                                        <a href="class/action.php?back_to_previos=1&rfnum=' . $array['rfnum'] . '" class="dropdown-item"><span class="me-2">
                                            <i class="ti-arrow-left"></i></span> Beladen / Entladen
                                        </a>
                                        <a class="dropdown-item opensendbox" data-bs-toggle="modal"
                                        alt="' . $array['rfnum'] . '%20' . $array['Nummer'] . '%20' . $array['Firma'] . '%20' . $array['Werknummer'] . '" data-bs-target="#messageModal" data-bs-whatever="@getbootstrap" href="#">
                                            <span class="me-2"><i class="ti-location-arrow"></i></span> Nachricht senden
                                        </a>
                                        <a href="#" class="dropdown-item set-unload-place" alt="' . $array['rfnum'] . '"><span class="me-2"><i class="ti-target"></i></span>Abladeplatz zuweisen</a>
                                        <a href="#" alt="' . $array['rfnum'] . ':' . $array['Nummer'] . '" class="dropdown-item set-forkbully" data-bs-toggle="modal" data-bs-target="#setSchipperModal"><span class="me-2"><i class="ti-target"></i></span> Stapler zuweisen</a>';
            $treepoints .= '<a type="button" class="dropdown-item prozess-done-warenannahme-stapler" alt="' . $array['rfnum'] . '" data="' . $array['Platz'] . '" data-toggle="modal" data-target="#confirmModal">
                                            <span class="me-2"><i class="ti-check"></i></span>Abfertigung (Stapler)
                                            </a>';
            $treepoints .= '<a type="button" class="dropdown-item open-leergut-mitnahme-dialog" data-bs-toggle="modal" data-bs-target="#diverseModal" alt="' . $array['rfnum'] . '">
                                            <span class="me-2"><i class="ti-alert"></i></span>Leergut Mitnahme
                                            </a>';
            $treepoints .= '<a type="button" class="dropdown-item open-maueller-auftrag-dialog" alt="' . $array['rfnum'] . '">
                                            <span class="me-2"><i class="ti-alert"></i></span>Zusatz Auftrag
                                            </a>';
            $treepoints .= '<a type="button" class="dropdown-item sendtonextstep" alt="' . $array['rfnum'] . ':' . $array['Nummer'] . ', ' . $array['Firma'] . '">
                                            <span class="me-2">
                                            <i class="ti-arrow-left"></i></span> Weiterleiten
                                            </a>';
            $treepoints .= '<div class="dropdown-divider"></div>
                                        <a href="#"  class="dropdown-item open-reklamation" alt="' . $array['rfnum'] . '" data-bs-toggle="modal" data-bs-target="#reklamation"><span class="me-2"><i class="ti-hand-stop"></i></span> Reklamation</a>
                                        <a href="#" class="dropdown-item create-qrcode" alt="' . $array['Nummer'] . '" title="' . $array['Firma'] . '" data-bs-toggle="modal" data-bs-target="#createQrcode"><span class="me-2"><i class="fa fa-qrcode fa-fw"></i></span> QR-Code</a>
                                        <a href="class/action.php?rfnum=' . $array['rfnum'] . '" class="dropdown-item remove-from-order"><span class="me-2"><i class="ti-trash"></i></span> löschen</a>
                                    </div>
                            </div>
                        </div>';
            if (!empty($array['Stapler'])):
                $showStapler = '<td class="pl-2">
                <a class="pictureviwever-show pointer link p-0" data="' . $array['Stapler']['BMI-Nummer'] . '"
                title="' . $array['Stapler']['BMI-Typ'] . ' ' . $array['Stapler']['Hersteller'] . ' ' . $array['Stapler']['BMI-Nummer'] . '" alt="db/' . $_SESSION['werknummer'] . "/bmi/" . $array['Stapler']['BMI-Bild'] . '"
                data-bs-toggle="modal" data-bs-target="#pictureviwever" alt="db/' . $_SESSION['werknummer'] . "/bmi/" . $array['Stapler']['BMI-Bild'] . '" src="db/' . $_SESSION['werknummer'] . "/bmi/TN" . $array['Stapler']['BMI-Bild'] . '"> ' . $array['Stapler']['Hersteller'] . ' ' . $array['Stapler']['BMI-Nummer'] . '
                 <i class="ti-gallery"></i></a>
                  </td>';
            endif;
            if (!empty($array['sofahnum'])) {
                $sonderfahrt = "bg-danger";
                $textSofsha = "text-white";
            }
            if (!empty($array['sofahnum']) && $array['Status'] == 100) {
                $sonderfahrt = "";
                $textSofsha = "";
            }
            if ($array['Status'] == 50 || $array['Status'] == 75 || $array['Status'] == 80 || $array['Status'] == 100):
                $check++;
                $header = "";
                switch ($array['Status']):
                    case 50:
                    case 75:
                    case 80:
                    case 501:
                        $cardcolor = "alert-success";
                        if ($_SESSION['weamanager_roll'] != "PforteUser") {
                            $header = '<div class="row">';
                            $header .= '<div class="col-5 p-0">';
                            if (empty($array['WA_Buro'])) {
                                $header .= '<button type="button" class="btn btn-primary rounded text-white border border-light p-2 prozess-done-warenannahme-buro" alt="' . $array['rfnum'] . '" data-toggle="modal" data-target="#confirmModal">Wareneingang (Büro)</button>';
                            }
                            if (!empty($array['Drive-In'] && $array['Status'] == 50)) {
                                switch ($array['Drive-In']) {
                                    case 1:
                                        $driveIn = '<i class="ti-truck"></i>fährt rein';
                                        $styleDriveIn = "btn-social-icon-text btn-twitter";
                                        break;
                                    case 2:
                                        $driveIn = "LKW fährt nicht rein";
                                        $styleDriveIn = "btn-outline-danger";
                                        break;
                                }
                                $header .= '<button  class="btn ' . $styleDriveIn . '" alt="' . $array['rfnum'] . '">' . $driveIn . '</button>';
                            }
                            if ($array['Status'] == 75 && empty($array['Weiterleitung'])) {
                                $header .= '<span class="spinner-grow text-primary me-2" role="status" title="LKW-Fahrer steht auf Entladeposition"><span class="visually-hidden"></span></span>';
                            }
                            if ($array['Status'] == 80 && empty($array['Weiterleitung'])) {
                                $header .= '<span class="spinner-grow text-success me-2" role="status" title="Stapler hat Be/Entladeprozess gestartet"><span class="visually-hidden"></span></span>';
                            }
                            if (!empty($array['Stapler'])) {
                                $header .= '<span class="pictureviwever-show ml-1 mr-1 pointer" 
                            data="' . $array['Stapler']['BMI-Nummer'] . "'&'" . $_SESSION['weamanageruser'] . '" 
                            title="' . $array['Stapler']['BMI-Nummer'] . '" 
                            alt="db/' . $_SESSION['werknummer'] . '/bmi/' . $array['Stapler']['BMI-Bild'] . '" 
                            data-bs-toggle="modal"
                            data-bs-target="#pictureviwever"><i class="fas fa-dolly"></i></span>';
                            }
                            if ($array['WA_Buro'] == "entfällt") {
                                $header .= '<span class="h5">' . $array['Prozessname'] . '</span>';
                            }
                            if ($array['Status'] == 75 && !empty($array['Weiterleitung'])) {
                                $header .= '<span class="h5 d-block">=> ' . $array['Weiterleitung'] . '<br></span>';
                            }
                            $header .= '</div>';
                            $header .= '<div class="col-6 pe-0 border-left border-right">';
                            $header .= '<span class="badge badge-info rounded me-1">' . $array['rfnum'] . '</span>';
                            if ($array['Ladung'] == "Paketdienst") {
                                $header .= '<div class="badge badge-info rounded me-1 pointer" title="Achtung Paketdienst!"><i class="ti-alert"></i> PD</div>';
                            }
                            if ($array['leergut_mitnahme'] == "JA") {
                                $header .= "<div class='badge badge-info rounded me-1 pointer' title='Achtung Leergutmitnahme!'><i class='ti-alert'></i> LGM</div>";
                            }
                            if (is_object(json_decode($array['leergut_mitnahme']))) {
                                $lgmtn = json_decode($array['leergut_mitnahme'], true);
                                $header .= "<div class='badge badge-secondary rounded me-1 pointer' title='Leergut: " . $lgmtn['Leergut-LT'] . "/" . $lgmtn['Leergut-Mng'] . " St.=>" . $lgmtn['set_stapler_leergut_unload'] . "'><i class='ti-check'></i> LGM</div>";
                            }
                            if (!empty($array['Prio'])) {
                                $header .= '<span class="badge rounded me-1 ' . $stylePrio[0] . '" title="' . $stylePrio[1] . ' ' . $array['Prio-Melder'] . '">' . $array['Prio'] . '</span>';
                            }
                            $header .= '<img src="assets/img/flage' . $array['Sprache'] . '.JPG" class="img-fluid rounded me-1" style="width:25px" title="' . $array['Sprache'] . '">';
                            if ($array['Zollgut'] == "JA") {
                                $header .= '<span class="badge badge-danger rounded me-1" title="Warten auf Zollabfertigung">Zoll</span>';
                            }
                            if ($array['Zollgut'] == "PASSIERT") {
                                $header .= '<span class="badge badge-success rounded me-1" title="Entladeerlaubnis erteilt">Zoll</span>';
                            }
                            if ($array['Zollgut'] == "CLEARING") {
                                $header .=  '<span class="badge badge-warning rounded me-1 cursor-default" title="' . $array['Zollmeldung']['Zollmeldung'] . '">Zoll</span>';
                            }
                            if ($array['Gefahrgut'] == "JA") {
                                $header .= '<span class="badge badge-danger rounded me-1 cursor-default" title="Gefahrgut: ' . $array['Gefahrgut'] . '/' . $array['Gefahrgutpunkte'] . ' Punkte">GG</span>';
                            }
                            if ($array['Ladung'] == "Versand Werk 5") {
                                $header .= '<span class="badge badge-info me-1  rounded">Vers-W5</span>';
                            }
                            if ($array['Ladung'] == "Transport für Werk 9") {
                                $header .= '<span class="badge badge-info me-1  rounded">Werk 9</span>';
                            }
                            $header .= $this->showMessages($array['rfnum']);
                            $header .= '</div>';
                            $header .= $treepoints;
                            $header .= '</div>';
                        }
                        break;
                    case 100:
                    case 1001:
                        $btnText = "Werksgelände verlassen";
                        $btnClass = "btn-primary";
                        $vehicle_gone = "vehicle_gone";
                        if (!empty($array['Protokoll_WA'])) {
                            $countFindings = $this->validateProtokoll(json_decode($array['Protokoll_WA'], true));
                            if ($countFindings > 0) {
                                $btnText = "Mängel beheben ($countFindings)";
                                $btnClass = "btn-danger text-white";
                                $vehicle_gone = "";
                            }
                        }
                        $cardcolor = "alert-warning";
                        if ($_SESSION['weamanager_roll'] != "PforteUser") {
                            $header = '
                        <div class="row">
                            <div class="col-5 p-0">
                            <form method="POST" action="class/action.php">
                                <input type="hidden" name="Status" value="120">
                                <input type="hidden" name="vehicle_gone" value="' . $array['rfnum'] . '">
                                <button type="button" class="btn ' . $btnClass . ' border rounded border-light text-light p-2 ' . $vehicle_gone . '">' . $btnText . '</button>
                            </form>
                            </div>
                            <div class="col-6 pe-0 border-left border-right">
                            <span class="badge badge-info rounded">' . $array['rfnum'] . '</span>
                            <img src="assets/img/flage' . $array['Sprache'] . '.JPG" class="img-fluid rounded me-1" style="width:25px" title="' . $array['Sprache'] . '">
                            </div>';
                            $header .= $treepoints;
                            $header .= '</div>';
                        }
                        break;
                    default:
                        $cardcolor = "alert-success";
                        break;
                endswitch;
                echo '<div class="card card-info card-order p-lg-0 rounded mb-2 ' . $cardcolor . '" alt="' . $array['rfnum'] . '" title="Laufnummer ' . $array['rfnum'] . '">
                <div class="card-header card-head-prozess p-2 ' . $cardcolor . ' ' . $sonderfahrt . '">' . $header . '</div>
                    <div class="card-body p-0 rounded card-height-small">
                        ' . $this->laufCard($array, $showStapler, $array['rfnum'], $array['Nummer']) . '
                    </div>
                </div>';
            endif;
        endforeach;
        if (empty($check)) {
            echo "<center><span class='small'>keine Daten</span></center>";
        }
    }
    private function is_registered($kenzNummer, $path)
    {
        require_once 'Sqlite.php';
        $sqlite = new Sqlite($_SESSION['werknummer'], $path);
        $expl = explode(" ", $kenzNummer);
        $q = "SELECT object FROM traffic";
        $results = $sqlite->sqliteSelect($q);
        foreach ($results as $result) {
            $toArray = json_decode($result['object'], true);
            if (trim($toArray['Nummer']) == trim($expl[1])) {
                return ["<span class='bagde badge-info p-1 rounded pointer set-unload-place' alt='" . $toArray['rfnum'] . "'>
            WN " . $toArray['rfnum'] . "
            </span>", $toArray['Platz']];
            }
        }
    }
    private function anKommend()
    {
        require_once 'connect.php';
        $o = new connect();
        $trange = time();
        $q = "SELECT object_runleaf, tstamp FROM werk_" . $_SESSION['werknummer'] . " WHERE tstamp > $trange AND anmeldeID IS NOT NULL AND done IS NULL ORDER BY tstamp DESC";
        $arraysExtern = $o->select($q);
        $arrays = [];
        if (!empty($arraysExtern)) {
            foreach ($arrays as $array) {
                $arr = json_decode($array['object_runleaf'], true);
            }
            $arrays[$array['tstamp']] = $arr;
        }
        $readDB = file_get_contents("../db/" . $_SESSION['werknummer'] . "/sonderfahrt.json");
        $arraysIntern = json_decode($readDB, true);
        if (!empty($arraysIntern)) {
            $arrays = $arraysIntern;
        }
        ksort($arrays);
        echo "<div class='table-responsive'>";
        if (!empty($arrays)) {
            echo "<table class='table'>";
            echo "<tr>
                <th >Datum</th>
                <th>Spedition</th>
                <th>Lieferant</th>                
                <th>Ladung</th>
                <th>Prio</th>
                <td></td>
                </tr>";
            foreach ($arrays as $key => $array) {
                $styleSofah = null;
                $prio = null;
                if (!empty($array['Prio'])) {
                    switch ($array['Prio']) {
                        case "Dringend":
                            $styleSofah = "class='alert-info'";
                            $prio = "D";
                            break;
                        case "Sehr dringend":
                            $styleSofah = "class='alert-danger'";
                            $prio = "SD";
                            break;
                    }
                }
                echo "<tr $styleSofah>";
                echo "<td class='align-middle'>" . $array['Lieferdatum'] . " " . $array['Zeitfenster'] . "</td>";
                echo "<td class='align-middle'>" . $array['Firma'] . "</td>";
                echo "<td class='align-middle'>" . $array['Lieferant'] . "</td>";
                echo "<td class='align-middle'>" . $array['Ladung'] . "</td>";
                echo "<td class='align-middle text-center'><span class='small'>" . $prio . "</span></td>";
                echo "<td><button type='button' class='btn btn-info p-1 show-preregist-data' alt='" . $key . "' data-bs-toggle='modal' data-bs-target='#preregist'><i class='fa fa-eye fa-fw'></i></button></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<table class='table customized-small'>";
            echo "<tr><td class='text-center'>keine Sonderfahrten angemeldet</td></tr>";
            echo "</table>";
        }
        echo "</div>";
    }
    public function vorAnmeldung()
    {

        require_once 'connect.php';
        $o = new connect();
        $startrange = time() - 86400;
        $endrange = time() + 172800;
        //WHERE kennzeichen='hegelmann'
        $q = "SELECT * FROM tracker ORDER BY tstamp DESC";
        $arraysExtern = $o->select($q);
        $arrays = [];
        if (!empty($arraysExtern)) {
            foreach ($arraysExtern as $results) {
                $arrays = json_decode($results['object_frz'], true);
                $spedition = ucfirst($results['spedition']);
                foreach ($arrays as $knz => $data) {

                    foreach ($data as $items) {

                        if (!empty($items['currentArrivalStamp'])) {
                            if ($items['currentArrivalStamp'] >= $startrange && $items['currentArrivalStamp'] < $endrange) {

                                if ($items['status'] == "On the Road") {
                                    $arr[$items['currentArrivalStamp']][$spedition . " " . $items['kennzeichen']][$items['sendungsnr']] = $items['colli'] . "&" . $items['weight'] . "&" . $items['kennzeichen'];
                                }
                                if (empty($items['status'])) {
                                    $arr[$items['currentArrivalStamp']][$spedition . " " . $items['kennzeichen']][$items['sendungsnr']] = $items['colli'] . "&" . $items['weight'] . "&" . $items['kennzeichen'] . "&" . $items['estUnloadTime'];
                                }
                            }
                        }
                    }
                }
            }
        }

        echo "<div class='table-responsive'>";
        if (!empty($arr)) {

            ksort($arr);

            echo "<table class='table'>";
            echo "<tr>
                <th>Datum</th>
                <th>Zeit</th>
                <th>Knz.</th>
                <th>Pos.</th>
                <th>Col.</th>
                <th>Gew.</th>
                <th>Info</th>
                </tr>";
            foreach ($arr as $preDatum => $arrays) {
                foreach ($arrays as $knz => $array) {
                    $anzSendungen = count($array);
                    $unloadTime = "";

                    foreach ($array  as $data) {
                        $onParking = $this->is_registered($knz, "../");
                        $expl = explode("&", $data);
                        $getColli[$knz][] = $expl[0];
                        $getWeight[$knz][] = str_replace(",", "", $expl[1]);
                        $getAutoKnz = $expl[2];
                        $unloadTime = $expl[3];
                    }
                    echo "<tbody class='table-striped'>";
                    if (empty($onParking[1])) {
                        echo "<tr>";
                        echo "<td class='align-middle'>" . date("d.m.", $preDatum) . "</td>";
                        echo "<td class='align-middle'>" . $unloadTime . "</td>";
                        echo "<td class='align-middle'>" . $knz . " " . $onParking[0] . "</td>";
                        echo "<td class='align-middle'>" . $anzSendungen . "</td>";
                        echo "<td class='align-middle'>" . array_sum($getColli[$knz]) . "</td>";
                        echo "<td class='align-middle'>" . array_sum($getWeight[$knz]) . " Kg.</td>";
                        echo "<td class='align-middle'>
                                <button class='btn btn-info p-1 smaller text-white more-information-preregister' data='" . $getAutoKnz . "' data-bs-toggle='modal' data-bs-target='#preRegisterInfo'><i class='ti-info'></i></button>
                                </td>";
                        echo "</tr>";
                    }


                    echo "</tbody>";
                }
            }
            echo "</table>";
        }
        echo "</div>";
    }
    public function getAnkommendList()
    {
        $this->anKommend();
        $this->vorAnmeldung();
    }
    public function getPreRegisterData($path = "")
    {
        $readDB = file_get_contents($path . "db/" . $_SESSION['werknummer'] . "/sonderfahrt.json");
        $preID = $_REQUEST['preID'];
        $arrays = json_decode($readDB, true);
        foreach ($arrays as $key => $array) {
            if ($key == $preID) {
                $filter = $array;
            }
        }
        unset($filter['rfnum']);
        unset($filter['Name Fahrer']);
        unset($filter['Legitimation']);
        unset($filter['anmeldeID']);
        unset($filter['FRZTyp']);
        unset($filter['Nummer']);
        unset($filter['Ladung']);
        unset($filter['Gefahrgutpunkte']);
        unset($filter['Zollgut']);
        unset($filter['kennzeichnugspflichtig']);
        unset($filter['Lieferschein']);
        unset($filter['Beladen für']);
        unset($filter['Sprache']);
        unset($filter['ladung_beschreibung']);
        unset($filter['Entladen']);
        unset($filter['Entladung']);
        unset($filter['Anmeldung']);
        unset($filter['timestamp']);
        unset($filter['Platz']);
        unset($filter['Status']);
        unset($filter['Protokoll_WA']);
        unset($filter['Protokoll_VERS']);
        unset($filter['Abfertigung']);
        unset($filter['gone']);
        unset($filter['alarm']);
        unset($filter['WA_Buro']);
        unset($filter['Lief_tstamp']);
        $filter['erstellt'] = date("d.m. H:i", $filter['erstellt']);
        echo "<div class='table-responsive'>";
        echo "<table class='table table-striped'>";
        foreach ($filter as $key => $value) {
            echo "<tr>";
            echo "<td>$key</td>";
            echo "<td>$value</td>";
            echo "</tr>";
        }
        echo "</tr>";
        echo "</table>";
        echo "</div>";
    }
    public function getDataFromTracker()
    {
        require_once 'Sqlite.php';
        $sqlite = new Sqlite($_SESSION['werknummer']);
        $q = "SELECT * FROM traffic";
        return $sqlite->sqliteSelect($q);
    }
    public function getDataFromArchiv($dayData)
    {
        require_once 'connect.php';
        $connect = new connect();
        $q = "SELECT object_runleaf as object FROM werk_" . $_SESSION['werknummer'];
        $results = $connect->select($q);
        foreach ($results as $result) {
            $toArray = json_decode($result['object'], true);
            $expl = explode(", ", $toArray['Anmeldung']);
            if ($dayData == $expl[0]) {
                $arrays[$toArray['rfnum']] = [
                    "object" => $result['object']
                ];
            }
        }
        return $arrays;
    }
    public function preRegisterEmailForm($path = "")
    {
        require_once $path . 'connect.php';
        $o = new connect();
        $q = "SELECT kennzeichen FROM tracker ORDER BY id ASC";
        $array = $o->select($q);
        echo '
        <div class="row justify-content-center">
            <div class="col-8">
                <h4>Voranmeldung hochladen</h4>
                <form action="class/action" enctype="multipart/form-data" method="post">
                    <div class="form-group mb-3">
                        <select class="form-control" name="folder" requiered>
                            <option value="">wählen</option>';
        foreach ($array as $item) {
            echo '<option value="' . $item['kennzeichen'] . '">' . ucfirst($item['kennzeichen']) . '</option>';
        }
        echo '</select>
                    </div>
                    <div class="form-group">
                        <input type="file" name="infomation_file" class="form-control" requiered>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="upload_preregister_information" value="1">
                        <input type="hidden" name="returnURI" value="../">
                        <button type="submit" class="btn btn-primary col-12 text-light">hochladen</button>
                    </div>
                </form>
            </div>
        </div>';
    }
    public function alert($getRequest)
    {
        echo '<div class="row justify-content-center" id="action-alerts">
            <div class="col-6">';
        switch ($getRequest) {
            case "success":
                echo '<alert class="text-light bg-success rounded border text-center shadow-lg pt-3 mb-2 p-3 d-block">Aktion erfolgreich</alert>';
                break;
            case "failed":
                echo '<alert class="text-light bg-danger rounded border text-center shadow-lg pt-3 mb-2 p-3 d-block">Aktion nicht erfolgreich</alert>';
                break;
        }
        echo '</div></div>';
    }
}