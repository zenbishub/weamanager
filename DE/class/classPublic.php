<?php


class classPublic
{

    public $inactivetimeEnd     = 4; //bis 5 Uhr ist Anmeldung nicht möglich
    public $inactivetimeStart   = 20; //von 21 Uhr bis 5 Uhr ist Anmeldung nicht möglich
    public $appname             = "maingate";
    public $arrayOfLanguages;
    private $scanFolder;

    public function __construct($scanDirectory = null)
    {
        if (empty(session_id())) {
            session_start();
        }
        $this->scanFolder = $scanDirectory;
        $this->arrayOfLanguages = [
            "DE" => "Deutsch",
            "EN" => "English",
            "FR" => "Français",
            "TR" => "Türkçe",
            "CZ" => "čeština",
            "RU" => "Русский",
            "IT" => "Italiano",
            "ES" => "Español",
            "PT" => "Português",
            "PL" => "Polski",
            "RO" => "Română",
            "UA" => "Українська",
            "BU" => "Български",
            "HR" => "Hrvatski",
            "HU" => "Magyar"
        ];
        if (!isset($_SESSION['frzlaufnummer'])) {
            $_SESSION['frzlaufnummer'] = null;
        }
        if (!isset($_SESSION['rfnum'])) {
            $_SESSION['rfnum'] = null;
        }
        if (!isset($_SESSION['wealanguage'])) {
            $_SESSION['wealanguage'] = "DE";
        }

        if (!isset($_SESSION['soundmodus'])) {
            $_SESSION['soundmodus'] = $this->setSoundModusByDefault("../");
        }
        if (isset($_COOKIE['frzlaufnummer'])) {
            $_SESSION['frzlaufnummer'] = $_COOKIE['frzlaufnummer'];
        }
        if (isset($_COOKIE['rfnum'])) {
            $_SESSION['rfnum'] = $_COOKIE['rfnum'];
        }
        if (isset($_COOKIE['adittionalJob'])) {
            $_SESSION['adittionalJob'] = $_COOKIE['adittionalJob'];
        }
        //print_R($_SESSION);
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
    private function readAddTasks($pfad = "")
    {
        $liteDB = new Sqlite($_SESSION['werknummer'], $pfad);
        $q = "SELECT * FROM add_tasks";
        return  $liteDB->sqliteSelect($q);
    }
    private function geoPosition($scanner)
    {
        require_once 'Sqlite.php';
        $sqlite = new Sqlite($_SESSION['werknummer']);
        $q = "SELECT latitude,longtitude FROM scanner WHERE IP LIKE '%$scanner'";
        $result = $sqlite->sqliteSelect($q);
        return $result;
    }
    public function setAdditionTask()
    {
        $results = $this->readAddTasks();
        foreach ($results as $result) {
            if ($result['bmi_nummer'] == $_SESSION['weamanageruser']) {
                $_SESSION['adittionalJob'] = $result['task'];
            }
        }
    }
    public function getEntladefuessSQLiteByRfnum($rfnum, $path = "class/", $pathTOdb = "")
    {
        if (!empty($rfnum)) {
            require_once $path . 'Sqlite.php';
            $liteDB = new Sqlite($_SESSION['werknummer']);
            $q = "SELECT * FROM entladefluess WHERE rfnum=$rfnum";
            $arrays = $liteDB->sqliteSelect($q);
            return json_encode($arrays, JSON_PRETTY_PRINT);
        }
    }
    private function setSoundModusByDefault($pfad = "")
    {
        $json = json_decode(file_get_contents($pfad . "db/soundmodus.json"), true);
        return $json['soundmodus'];
    }
    public function selectFormLanguage($pfad = "")
    {
        $langfile = strtolower($_SESSION['wealanguage']);
        $readDB = file_get_contents($pfad . "languages/$langfile/" . $langfile . "_formtext.json");
        return json_decode($readDB, true);
    }
    public function selectRunLeafLanguage($pfad = "")
    {
        $langfile = strtolower($_SESSION['wealanguage']);
        $readDB = file_get_contents($pfad . "languages/$langfile/" . $langfile . "_laufblatt.json");
        return json_decode($readDB, true);
    }
    public function selectDropDownOneLanguage($pfad = "")
    {
        $langfile = strtolower($_SESSION['wealanguage']);
        $readDB = file_get_contents($pfad . "languages/$langfile/" . $langfile . "_dropdown-one.json");
        return json_decode($readDB, true);
    }
    public function selectDropDownTwoLanguage($pfad = "")
    {
        $langfile = strtolower($_SESSION['wealanguage']);
        $readDB = file_get_contents($pfad . "languages/$langfile/" . $langfile . "_dropdown-two.json");
        return json_decode($readDB, true);
    }
    public function selectAlertLanguage($pfad = "")
    {
        $langfile = strtolower($_SESSION['wealanguage']);
        $readDB = file_get_contents($pfad . "languages/$langfile/" . $langfile . "_alerts.json");
        return json_decode($readDB, true);
    }
    public function chooseLanguage()
    {
        echo '<div class="row mb-5" id="startpage">';

        foreach ($this->arrayOfLanguages as $key => $languages) {
            echo '<div class="col-4 p-0 col-lg-3">
                <div class="card p-0 mb-3 p-1 shadow">
                    <div class="p-0 p-md-2 card-header h5">
                        ' . $languages . '
                    </div>
                    <div class="card-body pointer choose-language p-0 p-md-2" title="' . $key . '" alt="wea">
                        <img src="assets/img/flage' . $key . '.jpg" class="img-thumbnail">
                    </div>
                </div>
            </div>';
        }
        echo '</div>';
        exit;
    }
    public function scannerData($pfad = "")
    {
        $addNew = null;
        $expl = explode(".", $_SERVER['REMOTE_ADDR']);
        $scanner = "Nr.: " . $expl[2] . "." . $expl[3];
        $rfnum = $_REQUEST['rfnum'];
        $time = time();
        $readDB = json_decode(file_get_contents($pfad . "db/" . $_SESSION['werknummer'] . "/scannerstatus.json"), true);

        if (empty($readDB)) {
            $readDB['rfnum'] = $rfnum;
            $readDB['scanner'] = $scanner;
            $readDB['tstamp'] = $time;
            $toJson = json_encode([$readDB], JSON_PRETTY_PRINT);
            file_put_contents($pfad . "db/" . $_SESSION['werknummer'] . "/scannerstatus.json", $toJson);
            exit;
        }
        $countArray = count($readDB);
        foreach ($readDB as $key => $arrays) {

            if ($arrays['scanner'] == $scanner) {
                $readDB[$key]['rfnum'] = $rfnum;
                $readDB[$key]['tstamp'] = $time;
                $toJson = json_encode($readDB, JSON_PRETTY_PRINT);
                file_put_contents($pfad . "db/" . $_SESSION['werknummer'] . "/scannerstatus.json", $toJson);
                exit;
            }
            if ($arrays['rfnum'] != $rfnum) {
                $addNew = $rfnum;
            }
        }
        if (!empty($addNew)) {
            $readDB[$countArray]['rfnum'] = $rfnum;
            $readDB[$countArray]['scanner'] = $scanner;
            $readDB[$countArray]['tstamp'] = $time;
            $toJson = json_encode($readDB, JSON_PRETTY_PRINT);
            file_put_contents($pfad . "db/" . $_SESSION['werknummer'] . "/scannerstatus.json", $toJson);
            exit;
        }
    }
    public function setScannerNummer($pfad = "")
    {
        if (empty($_SESSION['rfnum'])) {
            return null;
        }
        $liteDB = new Sqlite($_SESSION['werknummer'], $pfad);
        $q = "SELECT object FROM traffic WHERE rfnum=" . $_SESSION['rfnum'] . "";
        $result = $liteDB->sqliteSelect($q);
        $readJsonAsArray = json_decode($result[0]['object'], true);
        $IP = $_SERVER['REMOTE_ADDR'];
        $expl = explode(".", $IP);
        $scannerNr = "Nr.: " . $expl[2] . "." . $expl[3];
        $readJsonAsArray['scanner'] = $scannerNr;
        $this->updateTraffic($_SESSION['rfnum'], $readJsonAsArray, $pfad);
    }
    public function soundAlert($id, $alarm, $status, $modus = 1, $alarm_stopped, $soundmodus, $ablagestelle, $animation, $lang, $Nummer)
    {

        if ($modus == 1 and $status == 50 and $alarm_stopped != $id) {
            if ($soundmodus == 1) {
                echo "<div class='sound'></div>";
                echo "<div class='row justify-content-center m-0 p-2 dirve_in_confirm'>
                <div class='col-12 p-1'>
                    <button class='btn btn-success p-3 pt-4 pb-4 col text-white stop-sound font-large' alt='$id' data='1' data-index=''>
                    <span class='ms-3'><i class='fas fa-truck'></i></span> " . $lang['string21'] . "</button>
                </div>
                <div class='col-12 p-1'>
                    <button class='btn btn-danger p-0 pt-4 pb-4  col text-white stop-sound font-large' alt='$id' data='2' data-index='" . $lang['string24'] . "'>
                    <span class='ms-3'><i class='far fa-frown'></i></span> " . $lang['string22'] . "</button>
                </div>
                </div>";
            }
            echo "<div id='overlay-warning' class='row mb-2 justify-content-center'>
                    <div class='col-12 p-0'>";
            echo "<div id='warning-to-driver' class='alert-warning p-2 rounded text-center border'>
                <h1>" . $Nummer . "</h1>
                " . $lang['string13'] . "
                        <span class='d-block mt-1 h3'>$ablagestelle</span>
                        <span class='ms-2'>
                        <button type='button' title='$ablagestelle' alt='$animation' data='$id' class='btn btn-info view-locations-and-stop-sound text-white' id='showlocation'>
                        " . $lang['string15'] . " <i class='ti-location-pin'></i></button></span>
                        </div>
                    </div>
                </div>";
        }
    }
    public function zollAlert($rfnum, $zollgut, $status, $Zollabfertigung, $zoll_alarm_stopped, $lang)
    {

        if (!empty($Zollabfertigung) and $status < 50 and $status == "" and $zoll_alarm_stopped != $rfnum) {

            echo "<div class='sound'></div>";
            echo "<div class='row justify-content-center mb-2'>
                <div class='col-6 text-center'>
                    <button class='btn btn-icon btn-rounded btn-success text-white stop-zoll-sound font-large' alt='$rfnum'><i class='fas fa-volume-mute'></i></button>
                </div></div>";

            echo "<div id='overlay-warning' class='row mb-2 justify-content-center'>
            <div class='col-12 p-0'>
            <span class='text-white font-larger' id='zoll-close-alert' alt='" . $rfnum . "'><i class='ti-close'></i></span>
                <div id='warning-to-driver' class='alert-warning p-2 rounded text-center border'>" . $lang['string16'] . "
                </div>
            </div>
        </div>";
        }
    }
    private function animationAbladestelle($abladestelle)
    {
        $arrays = $this->getLocations();
        foreach ($arrays as $array) {
            if ($array['Platz'] == $abladestelle) {
                return $array['Video'];
            }
        }
    }
    private function getLocations()
    {
        $readDB = file_get_contents("../db/" . $_SESSION['werknummer'] . "/entladestellen.json");
        return json_decode($readDB, true);
    }
    private function soundModus($pfad = "")
    {
        $json = json_decode(file_get_contents($pfad . "db/soundmodus.json"), true);
        return $json['soundmodus'];
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
    public function getAllKnumm($getOrderList)
    {
        echo  implode(",", array_unique($getOrderList));
    }
    public function readLaufnummer()
    {
        if (isset($_REQUEST['register'])) {
            echo $this->alerts("Fahrzeug erfolgreich hinzugefügt");
        }
        echo '<div class="card mt-5 pl-0 pr-0" id="start-lauf">
        <div class="card-header">
            Zum Starten Autokennzeichen eingeben
        </div>
        <form method="post" action="class/action.php">
        <div class="card-body">
        <div class="row">
            <div class="col-8 ps-0">
            <input type="search" class="form-control" name="session_nummer" id="session_nummer" placeholder="Autokennzeichen" required>
            <input type="hidden" name="appname" value="wea">
            </div>
        </div>
        </div>
        <div class="card-footer">
        <button class="btn btn-dark">
            ok
        </button>
        </div>
        </form>
        </div>';
    }
    public function checkInactiveTime($inactiveTimeStart, $inactiveTimeEnd, $path = "")
    {
        if (isset($_REQUEST['ajaxCheckInactiveTime']) || $_REQUEST['ajaxPforteMonitorEinfahrt']) {
            if (idate("H") > $inactiveTimeStart && idate("H") < $inactiveTimeEnd) {
                exit;
            }
        }
        if (idate("H") > $inactiveTimeStart && idate("H") < $inactiveTimeEnd) {
            return true;
        }
        return false;
    }
    private function getLinkToLieferDokumenten($wartenummer, $knznummer)
    {
        $folder = array_diff(scandir("../" . $this->scanFolder), array('..', '.'));
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
    public function warteschlange($getOrderList, $getOrderMAautrag, $pfad = "", $location = "")
    {
        krsort($getOrderList);
        $lang = $this->selectRunLeafLanguage("../");
        if ($location == "stapler"):
            $this->laufCardManuellerAuftragIntern($getOrderMAautrag);
        endif;

        foreach ($getOrderList as $array):
            $protokol_WA = json_decode($array['Protokoll_WA']);
            if ($array['Status'] != 120):

                if ($array['rfnum'] == $_SESSION['rfnum'] && $array['Nummer'] == $_SESSION['frzlaufnummer'] && $location == "wea"):
                    $expl = explode(" ", $array['Anmeldung']);
                    $laufnummer = null;
                    $abladestelle = null;
                    if ($array['alarm'] == null) {
                        $_SESSION['alarm_stopped'] = 0;
                    }
                    if (!empty($array['Platz'])) {
                        foreach ($this->getLocations() as $values) {
                            //echo $values['Platz'];
                            if ($values['Platz'] == $array['Platz']) {
                                $abladestelle = $values['Video'];
                            }
                        }
                    }
                    $this->laufSteps($array['Status']);
                    $this->laufCard($expl, $array, $laufnummer, $pfad, "submit", $location, $lang, $abladestelle);
                    if ($location == "wea") {
                        echo '<div class="card-footer text-start">
                        <p><span class="font-larger me-2 text-danger"><i class="ti-alert"></i></span>' . $lang['string17'] . '</p>
                        </div>';
                    }
                endif;
                if (($location == "stapler" and $array['Stapler']['BMI-Nummer'] == $_SESSION['weamanageruser'] and $array['Status'] < 100) ||
                    ($location == "stapler" and $array['Stapler']['BMI-Nummer'] == $_SESSION['weamanageruser'])
                ):
                    $laufnummer = null;
                    $expl = explode(" ", $array['Anmeldung']);
                    if (empty($protokol_WA->signed)) {
                        $this->laufCard($expl, $array, $laufnummer, $pfad, "submit", $location, $lang);
                    }
                endif;
                if (($location == "stapler" and $array['Stapler']['BMI-Nummer'] == $_SESSION['weamanageruser']) ||
                    ($location == "stapler" and $array['MA-Autfrag']['stapler_for_auftrag'] == $_SESSION['weamanageruser'])
                ):
                    $this->laufCardManuellerAuftrag($array, $lang);
                endif;
                if (($array['leergut_mitnahme'] != "NEIN" && $array['Status'] < 100)):
                    $jsonArray = json_decode($array['leergut_mitnahme'], true);
                    if ($jsonArray['set_stapler_leergut_unload'] == $_SESSION['weamanageruser'] && $location == "stapler" && empty($jsonArray['person_sign'])):
                        $this->laufCardLeerGut($array, $location, $lang);
                    endif;
                    if ($array['rfnum'] == $_SESSION['rfnum'] && $location == "wea" && $array['Status'] < 100 && empty($jsonArray['person_sign'])):
                        $this->laufCardLeerGut($array, $location, $lang);
                    endif;
                endif;
            endif;
        // if(($array['leergut_mitnahme']!="NEIN" && $array['Status']<100)):
        //     $jsonArray = json_decode($array['leergut_mitnahme'],true);
        //         if($jsonArray['set_stapler_leergut_unload']==$_SESSION['weamanageruser'] && $location=="stapler" && empty($jsonArray['person_sign'])):
        //             $this->laufCardLeerGut($array,$location,$lang);
        //         endif;
        //         if($array['rfnum']==$_SESSION['rfnum'] && $location=="wea" && $array['Status']<100 && empty($jsonArray['person_sign'])):
        //             $this->laufCardLeerGut($array,$location,$lang);
        //         endif;
        // endif;
        endforeach;
    }
    private function laufSteps($status = "")
    {
        switch ($status):
            case "":
                $step = "<div class='col-1 h4 alert-success text-center rounded-3 p-0'>1</div>
                <div class='col-2 h4  text-center p-0'>---></div>
                <div class='col-1 h4 text-black-20 text-center p-0'>2</div>
                <div class='col-2 h4 text-black-20 text-center p-0'>---></div>
                <div class='col-1 h4 text-black-20 text-center p-0'>3</div>
                <div class='col-2 h4 text-black-20 text-center p-0'>---></div>
                <div class='col-1 h4 text-black-20 text-center p-0'>4</div>";
                break;
            case "50":
                $step = "<div class='col-1 h4 alert-success text-center rounded-3 p-0'>1</div>
                <div class='col-2 h4  text-center p-0'>---></div>
                <div class='col-1 h4 alert-success text-center rounded-3 p-0'>2</div>
                <div class='col-2 h4 text-black-20 text-center p-0'>---></div>
                <div class='col-1 h4 text-black-20 text-center p-0'>3</div>
                <div class='col-2 h4 text-black-20 text-center p-0'>---></div>
                <div class='col-1 h4 text-black-20 text-center p-0'>4</div>";
                break;
            case "75":
                $step = "<div class='col-1 h4 alert-success text-center rounded-3 p-0'>1</div>
                <div class='col-2 h4  text-center p-0'>---></div>
                <div class='col-1 h4 alert-success text-center rounded-3 p-0'>2</div>
                <div class='col-2 h4 text-center p-0'>---></div>
                <div class='col-1 h4 alert-success text-center rounded-3 p-0'>3</div>
                <div class='col-2 h4 text-black-20 text-center p-0'>---></div>
                <div class='col-1 h4 text-black-20 text-center p-0'>4</div>";
                break;
            case "100":
                $step = "<div class='col-1 h4 alert-success text-center rounded-3 p-0'>1</div>
                <div class='col-2 h4 text-center p-0'>---></div>
                <div class='col-1 h4 alert-success text-center rounded-3 p-0'>2</div>
                <div class='col-2 h4 text-center p-0'>---></div>
                <div class='col-1 h4 alert-success text-center rounded-3 p-0'>3</div>
                <div class='col-2 h4 text-center p-0'>---></div>
                <div class='col-1 h4 alert-success text-center rounded-3 p-0'>4</div>";
                break;
            default:
                $step = "";
                break;
        endswitch;
        echo  "<div class='row justify-content-between p-2 bg-white rounded border laufstaps'>" . $step . "</div>";
    }
    private function modifyText($text)
    {
        if (empty($text)) {
            return null;
        }
        $text = str_replace("/", " ", $text);
        $expl = explode(" ", $text);
        $newLine = [2, 4, 6, 9, 11, 13, 15, 17, 19, 21, 23];
        if (count($expl) > 3) {
            $return = "";
            foreach ($expl as $i => $word) {
                if (in_array($i, $newLine)) {
                    $return .= "<br>";
                }
                $return .= $word . " ";
            }
            return $return;
        }
        return $text;
    }
    private function laufCard($expl, $array, $laufnummer, $pfad, $entladebtn, $location, $lang, $abladestelle = "")
    {
        $protokol_WA = json_decode($array['Protokoll_WA']);
        $protokol_VERS = $array['Protokoll_WA'];
        $reklamation = $array['Reklamation'];
        $abfertigung = null;
        $leergut_laden = null;
        $zollmeldung = null;
        $Zollabfertigung = null;
        $animation = $this->animationAbladestelle($array['Platz']);
        if (!empty($protokol_WA->signed)) {
            $abfertigung = "bestätigt";
        }
        if (!empty($array['Zollmeldung'])) {
            $zollmeldung = "<br>" . $array['Zollmeldung']['Grund'] . "<p class='small'>" . $this->modifyText($array['Zollmeldung']['Zollmeldung']) . "</p>";
        }
        switch ($array['Status']):
            case null:
                $classRequire = "alert-info";
                $footer = "";
                if ($location == "wea") {
                    $zoll_alarm_stopped = $_SESSION['zoll_alarm_stopped'];
                    $Zollabfertigung = $array['Zollabfertigung'];
                    $this->zollAlert($array['rfnum'], $array['Zollgut'], $array['Status'], $Zollabfertigung, $zoll_alarm_stopped, $lang);
                }
                break;
            case 50:
                $classRequire = "alert-warning";
                if ($location == "stapler" && $array['Drive-In']) {
                    $footer = '<div class="card-footer text-center">
                        <form method="POST" action="class/action.php" class="prozessing">
                            <input type="hidden" name="return_uri" value="' . $location . '">
                            <input type="hidden" name="Status" value="80">
                            <input type="hidden" name="entry_passed" id="entry_passed" value="' . $array['rfnum'] . '">
                            <button type="' . $entladebtn . '" class="btn btn-primary otacity-25 pass-througt-entry">' . $lang['string23'] . '</button>
                        </form>
                        </div>';
                }
                if ($location == "wea") {
                    $footer = '<div class="card-footer text-center">
                    <form method="POST" action="class/action.php" class="prozessing">
                        <input type="hidden" name="return_uri" value="' . $location . '">
                        <input type="hidden" name="Status" value="75">
                        <input type="hidden" name="entry_passed" id="entry_passed" value="' . $array['rfnum'] . '">
                        <button type="' . $entladebtn . '" class="btn btn-primary otacity-25 pass-througt-entry">' . $lang['string12'] . '</button>
                    </form>
                    </div>';
                    $alarm_stopped = $_SESSION['alarm_stopped'];
                    $this->soundAlert($array['rfnum'], $array['alarm'], $array['Status'], $this->soundModus("../"), $alarm_stopped, $this->soundModus("../"), $array['Platz'], $animation, $lang, $array['Nummer']);
                }
                break;
            case 75:
            case 80:
                $classRequire = "alert-success";
                $footer = '<div class="card-footer"><div class="row"><div class="col-12 text-center">';
                if (empty($array['WA_Buro'])) {
                    $footer .= '<button type="button" class="btn btn-secondary opacity-50 text-white border border-light prozess-done-warenannahme-stapler rounded" alt="' . $array['rfnum'] . '" data="' . $array['Platz'] . '">
                        ' . $lang['string14'] . '
                        </button>';
                }
                if (!empty($array['WA_Buro']) && $abfertigung != "bestätigt") {
                    $footer .= '<button type="button" class="btn btn-primary text-white border border-light prozess-done-warenannahme-stapler rounded" alt="' . $array['rfnum'] . '" data="' . $array['Platz'] . '" data-bs-toggle="modal" data-bs-target="#confirmModal">
                        ' . $lang['string14'] . '
                        </button>';
                }
                $footer .= '</div>';
                $footer .= '</div></div>';
                break;
            case 100:
                $btnText = "Werksgelände verlassen";
                $btnClass = "btn-primary";
                if (!empty($protokol_WA)) {
                    $countFindings = $this->validateProtokoll(json_decode($array['Protokoll_WA'], true));
                    if ($countFindings > 0) {
                        $btnText = "Mängel beheben ($countFindings)";
                        $btnClass = "btn-danger text-white";
                    }
                }
                $classRequire = "alert-warning";
                $footer = '<div class="card-footer text-center">
                <form method="POST" action="class/action.php" class="prozessing">
                    <input type="hidden" name="return_uri" value="public">
                    <input type="hidden" name="Status" value="120">
                    <input type="hidden" name="vehicle_gone" value="' . $array['rfnum'] . '">
                    <button type="button" data="' . $array['rfnum'] . '" data-bs-toggle="modal"
                                        data-bs-target="#simpleAlertModal" id="form-leave-plant" class="btn ' . $btnClass . ' border border-light vehicle_gone" alt="werkverlassen">' . $btnText . '</button>
            </form>
            </div>';
                break;
            default:
                $classRequire = "alert-success";
                $footer = "";
                break;
        endswitch;
        echo '
        <div class="col-12 p-0">
        <div class="card ' . $classRequire . ' mb-4 font-large shadow">
            <div class="card-header ps-0 pe-0">
            <div class="row justify-content-end p-0">';
        if ($location == "wea") {

            $lieferDokumente = $this->getLinkToLieferDokumenten($array['rfnum'], $array['Nummer']);

            echo '<div class="col-2 p-0 text-center">
                        <button type="button" class="btn-rounded btn-success text-white rounded border p-0 ps-2 pe-2" id="take-image-later" data-bs-toggle="modal" data-bs-target="#diverseModal" data-index="lieferschein">
                            <span class="font-large"><i class="ti-camera"></i></span>
                        </button>
                        <input type="hidden" id="rfnum_autor" value="' . $array['rfnum'] . '">
                        <input type="hidden" id="knznummer_autor" value="' . $array['Nummer'] . '">
                        <input type="hidden" id="picture-autor" value="wea">
                    </div>';
        }
        echo '<div class="col-4 p-0">';
        if ($array['leergut_mitnahme'] == "JA") {
            echo "<div class='badge badge-info rounded font-large'>LGM</div>";
        }
        if (is_object(json_decode($array['leergut_mitnahme']))) {
            echo "<div class='badge badge-secondary rounded me-1 pointer font-large' title='" . $array['leergut_mitnahme'] . "'><i class='ti-check'></i> LGM</div>";
        }
        echo '</div>';
        echo '<div class="col-2 p-0">';
        if (!empty($array['Prio'])) {
            echo '<span class="badge badge-danger rounded font-large">' . $array['Prio'] . '</span>';
        }
        echo '</div>';

        echo '<div class="col-1 p-0"><span class="small float-end badge badge-info rounded font-large" id="order-waitnumber">' . $array['rfnum'] . '</span></div>';
        echo '<div class="col-2">
                <span class=""><img src="assets/img/flage' . $array['Sprache'] . '.JPG" class="img-thumbnail" style="width:40px"></span>
                </div>';
        if ($location == "stapler" || $location == "wea") {
            echo '<div class="dropdown no-arrow col-1 p-0 text-end">
                        <a class="dropdown-toggle clear-interval-improzess font-large" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-primary"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" style="">
                            <div class="dropdown-header">Actions</div>';
            if ($array['Status'] > 25) {
                echo '<a href="class/action.php?back_to_previos=1&rfnum=' . $array['rfnum'] . '&return=' . $location . '" class="dropdown-item"><span class="me-2">
                                    <i class="ti-arrow-left"></i></span> Beladen / Entladen
                                </a>
                              <a type="button" class="dropdown-item sendtonextstep" alt="' . $array['rfnum'] . ':' . $array['Nummer'] . ', ' . $array['Firma'] . '">
                                            <span class="me-2">
                                            <i class="ti-arrow-left"></i></span> Weiterleiten
                                            </a>';
            }
            echo '
                            <a href="#" class="dropdown-item open-reklamation" alt="' . $array['rfnum'] . '" data-bs-toggle="modal" data-bs-target="#reklamation"><span class="me-2"><i class="ti-hand-stop"></i></span> Reklamation</a>
                            <div class="dropdown-divider"></div>
                            <a href="class/action.php?delete_by_maingate=1&rfnum=' . $array['rfnum'] . '&returnUri=wea" 
                                class="dropdown-item remove-from-order" id="form-leave-plant" alt="werkverlassen_ohne_prozess" data="' . $array['rfnum'] . '" data-bs-toggle="modal" data-bs-target="#simpleAlertModal"><span class="me-2">
                                <i class="ti-trash"></i></span> Löschen
                            </a>

                        </div>
                    </div>';
        }
        echo '</div></div>
            <div class="card-body p-2 bg-white ">
                <div class="row justify-content-center overflow-auto">
                    <div class="p-0 ">
                        <table class="p-0 m-0 table table-striped customized-normal laufschein" alt="' . $array['rfnum'] . ':' . $array['Nummer'] . ':' . $array['Werknummer'] . '">';
        $lieferDokumente = $this->getLinkToLieferDokumenten($array['rfnum'], $array['Nummer']);
        if ($array['Status'] == 75 && !empty($array['Weiterleitung'])) {
            echo '<tr class="bg-info text-center"><td class="h3 p-2 text-light small" colspan="2">Weiterleitung an ' . $array['Weiterleitung'] . '</td></tr>';
        }
        if (!empty($lieferDokumente)) {
            echo '<tr>
                                <td class="small-viewport">Scan</td>
                                <td>Uploaded</td>
                                </tr>';
        }
        if (!empty($array['Lieferschein'])) {
            echo '<tr>
                                    <td class="small-viewport">' . $lang['string18'] . '</td>
                                    <td class="ps-2">
                                        <a class="pointer pictureviwever-show modal-xl btn btn-primary p-1 controls" data-bs-toggle="modal" data-bs-target="#pictureviwever" alt="db/' . $_SESSION['werknummer'] . "/img_temp/" . $array['Lieferschein'] . '" data="' . $array['Stapler']['BMI-Nummer'] . '&' . $_SESSION['weamanageruser'] . '">Foto <i class="ti-eye"></i></a>
                                    </td> 
                                </tr>';
        }
        if (!empty($array['Prozessname'])) {
            echo '<tr>
                                <td class="small-viewport">Prozess</td><td class="ps-2">' . $array['Prozessname'] . '</td> 
                            </tr>';
        }
        if (!empty($array['customized_time'])) {
            echo '<tr>
                                <td class="small-viewport">' . $lang['string25'] . '</td><td class="ps-2">' . $array['customized_time'] . '</td> 
                            </tr>';
        }
        echo '<tr>
                                <td class="small-viewport">' . $lang['string1'] . '</td><td class="ps-2">' . $array['Firma'] . '</td> 
                            </tr>
                            <tr>
                                <td class="small-viewport">' . $lang['string2'] . '</td><td class="ps-2">' . $array['Platz'];
        if (!empty($abladestelle)) {
            echo '<span class="ms-2"><button type="button" title="' . $array['Platz'] . '" alt="' . $abladestelle . '" class="btn bg-info btn-rounded p-1 font-large text-white" id="showlocation">
                                    <i class="ti-location-pin"></i></button></span>';
        }
        echo '</td>
                            </tr>
                            <tr>
                                <td class="small-viewport">' . $lang['string3'] . '</td><td class="ps-2">' . $array['Nummer'] . '</td>
                            </tr>
                            <tr>
                                <td class="small-viewport">' . $lang['string4'] . '</td><td class="ps-2">' . $array['Name Fahrer'] . '</td>
                            </tr>
                            <tr>
                                <td class="small-viewport">' . $lang['string5'] . '</td><td class="ps-2">' . $array['Zollgut'] . ' ' . $zollmeldung . '</td>
                            </tr>';
        if (!empty($array['Zoll-Sendungen'])) {
            echo '<tr>
                                <td class="small-viewport">Zoll/Info</td><td>' . $array['Zoll-Sendungen']['Sendungen'] . ' / ' . $this->modifyText($array['Zoll-Sendungen']['Collis']) . '</td>
                            </tr>';
        }
        echo '<tr>
                                <td class="small-viewport">' . $lang['string6'] . '</td><td class="ps-2">' . $array['Ladung'] . '</td>
                            </tr>';
        if (!empty($array['ladung_beschreibung'])) {
            echo '<tr>
                                <td class="small-viewport"></td><td class="ps-2 text-truncate">' . $this->modifyText($array['ladung_beschreibung']) . '</td>
                            </tr>';
        }
        echo '<tr>
                                <td class="small-viewport">' . $lang['string7'] . '</td><td class="ps-2">' . $array['Beladen für'] . '</td>
                            </tr>
                            <tr>
                                <td class="small-viewport">' . $lang['string9'] . '</td><td class="ps-2">' . $array['Gefahrgut'] . '</td>
                            </tr>';
        if (!empty($array['Weiterleitung_von'])) {
            echo '<tr><td class="small-viewport">WL von</td><td class="ps-2">' . $array['Weiterleitung_von'] . '</td></tr>';
        }
        echo '<tr>
                                <td class="small-viewport">' . $lang['string11'] . '</td><td class="ps-2">' . $abfertigung . '</td>
                            </tr>
                            <tr>
                                <td class="small-viewport">Werkschutz</td><td class="ps-2 small-viewport">
                                ' . date("d.m, H:i", $array['LegitimationConfirm']) . '</td>
                            </tr>';
        if ((!empty($reklamation) && $location == "stapler") || (!empty($reklamation) && $location == "wea")) {
            echo '<tr><td colspan="2"><h4>Reklamation</h4>';
            echo '<div class="row mt-3">';
            echo '<div class="col-12 p-0 mb-2 small">';
            echo $reklamation['reklamation_beschreibung'];
            echo '</div>';
            foreach ($reklamation['reklamation_bilder'] as $bilder) {
                echo '<div class="col-4 p-0">
                                        <img data-bs-toggle="modal" data="' . $array['Stapler']['BMI-Nummer'] . '&' . $_SESSION['weamanageruser'] . ' title="' . $reklamation['reklamation_beschreibung'] . '" data-bs-target="#pictureviwever" alt="db/' . $_SESSION['werknummer'] . '/reklamation/' . $bilder . '" class="pointer img-thumbnail pictureviwever-show controls modal-xl" src="db/' . $_SESSION['werknummer'] . '/reklamation/TN' . $bilder . '">
                                        </div>';
            }
            echo '</div>';
            echo '</td><tr>';
        }
        echo '</table>
                    </div>
                    <div class="col-4">';
        echo $laufnummer;
        echo '</div>
                    </div>
                </div>';
        echo $footer;
        echo '</div>';
        echo '</div>';
        echo '</div>
        </div>';
    }
    private function laufCardLeerGut($array, $location, $lang)
    {
        $decodeArray = json_decode($array['leergut_mitnahme'], true);
        echo '<div class="col-12 p-0 pb-5">
        <div class="card p-0 mb-4 font-large shadow">
        <div class="card-header ps-0">
            <div class="row justify-content-end">';
        echo '<div class="col-8 p-0">';
        echo '<span class="h4 ps-2">Leergutmitnahme</span>';
        echo '</div>';
        echo '<div class="col-2"><span class="small float-end badge badge-info rounded font-large">' . $array['rfnum'] . '</span></div>';
        echo '<div class="col-1 p-0">
                <span class=""><img src="assets/img/flage' . $array['Sprache'] . '.JPG" class="img-thumbnail" style="width:40px"></span>
                </div>';
        if ($location == "stapler") {
            echo '<div class="dropdown no-arrow col-1 p-0 text-end">
                        <a class="dropdown-toggle clear-interval-improzess font-large" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-primary"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" style="">
                            <div class="dropdown-header">Actions</div>
                            <div class="dropdown-divider"></div>
                            <a href="#" class="dropdown-item open-reklamation" alt="' . $array['rfnum'] . '" data-bs-toggle="modal" data-bs-target="#reklamation"><span class="me-2"><i class="ti-hand-stop"></i></span> Reklamation</a>
                        </div>
                    </div>';
        }
        echo '</div></div>
        <div class="card-body p-2">
            <div class="table-responsive p-0">
            <table class="p-0 m-0 table table-striped customized-normal laufschein" alt="' . $array['rfnum'] . ':' . $array['Nummer'] . ':' . $array['Werknummer'] . '">
                <tr>
                    <td class="small-viewport">' . $lang['string1'] . '</td><td class="ps-2">' . $array['Firma'] . '</td> 
                </tr>
                <tr>
                    <td class="small-viewport">' . $lang['string3'] . '</td><td class="ps-2">' . $array['Nummer'] . '</td>
                </tr>
                <tr>
                    <td class="small-viewport">Ladungsträger</td><td class="ps-2">' . $decodeArray['Leergut-LT'] . '</td>
                </tr>
                <tr>
                    <td class="small-viewport">Menge</td><td class="ps-2">' . $decodeArray['Leergut-Mng'] . '</td>
                </tr>';
        if (!empty($reklamation) && $location == "stapler") {
            echo '<tr><td colspan="2"><h5>Reklamation</h5>';
            echo '<div class="row mt-3">';
            echo '<div class="col-12 p-0 mb-2">';
            echo $reklamation['reklamation_beschreibung'];
            echo '</div>';
            foreach ($reklamation['reklamation_bilder'] as $bilder) {
                echo '<div class="col-3 p-0"><img data-bs-toggle="modal" title="' . $reklamation['reklamation_beschreibung'] . '" data-bs-target="#pictureviwever" alt="db/' . $_SESSION['werknummer'] . '/reklamation/' . $bilder . '" class="pointer img-thumbnail pictureviwever-show controls" src="db/' . $_SESSION['werknummer'] . '/reklamation/TN' . $bilder . '"></div>';
            }
            echo '</div>';
            echo '</td><tr>';
        }
        echo '</table>
        </div>
        </div>
        <div class="card-footer border-0">
        <div class="row"><div class="col-12 text-center">';
        if ($array['Status'] > 50 && empty($array['WA_Buro'])) {
            echo '<button type="button" class="btn btn-secondary opacity-50 text-white border border-light rounded" alt="' . $array['rfnum'] . '">
            ' . $lang['string14'] . '
            </button>';
        }
        if ($array['Status'] > 25 && $array['Status'] < 100) {
            echo '<button type="button" class="btn btn-primary text-white border border-light prozess-done-leergutmitnahme-stapler rounded" alt="' . $array['rfnum'] . '" data-toggle="modal" data-target="#confirmModal">
            ' . $lang['string14'] . '
            </button>';
        }
        echo '</div>
        </div>
        </div>
        </div>';
    }
    private function laufCardManuellerAuftrag($array, $lang)
    {
        if ($array['MA-Autfrag']['stapler_for_auftrag'] == $_SESSION['weamanageruser'] && empty($array['MA-Autfrag']['person_sign'])) {
            echo '<div class="col-12 p-0">
            <div class="card p-0 mb-4 font-large shadow">
                <div class="card-header ps-0">
                    <div class="row justify-content-end">';
            echo '<div class="col-8 p-0">';
            echo '<span class="h4 ps-2">Manueller Auftrag</span>';
            echo '</div>';
            echo '<div class="col-2"><span class="small float-end badge badge-info rounded font-large">' . $array['rfnum'] . '</span></div>';
            echo '<div class="col-1 p-0">
                        <span class=""><img src="assets/img/flage' . $array['Sprache'] . '.JPG" class="img-thumbnail" style="width:40px"></span>
                        </div>';
            echo '<div class="dropdown no-arrow col-1 p-0 text-end">
                                <a class="dropdown-toggle clear-interval-improzess font-large" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-primary"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" style="">
                                    <div class="dropdown-header">Actions</div>
                                    <div class="dropdown-divider"></div>
                                    <a href="#" class="dropdown-item open-reklamation" alt="' . $array['rfnum'] . '" data-bs-toggle="modal" data-bs-target="#reklamation"><span class="me-2"><i class="ti-hand-stop"></i></span> Reklamation</a>
                                </div>
                            </div>';
            echo '</div></div>
                <div class="card-body p-2">
                    <div class="table-responsive p-0">
                    <table class="p-0 m-0 table table-striped customized-normal laufschein" alt="' . $array['rfnum'] . ':' . $array['Nummer'] . ':' . $array['Werknummer'] . '">
                        <tr>
                            <td class="small-viewport">' . $lang['string1'] . '</td><td class="ps-2">' . $array['Firma'] . '</td> 
                        </tr>
                        <tr>
                            <td class="small-viewport">' . $lang['string3'] . '</td><td class="ps-2">' . $array['Nummer'] . '</td>
                        </tr>
                        <tr>
                            <td class="small-viewport">Umfang</td><td class="ps-2">' . $array['MA-Autfrag']['umfang'] . '</td>
                        </tr>';
            echo '</table>
                    </div>
                </div>
                <div class="card-footer border-0">
                <div class="row"><div class="col-12 text-center">';

            if ($array['Status'] > 25) {
                echo '<button type="button" class="btn btn-primary text-white border border-light prozess-done-manueller-auftrag-stapler rounded" alt="' . $array['rfnum'] . '" data-toggle="modal" data-target="#confirmModal">
                    ' . $lang['string14'] . '
                    </button>';
            }
            echo '</div>
                </div>
                </div>
            </div>';
        }
    }
    private function laufCardManuellerAuftragIntern($arrays)
    {
        foreach ($arrays as $index => $array) {

            if ($array['stapler_for_auftrag'] == $_SESSION['weamanageruser'] || $array['stapler_for_auftrag'] == $_SESSION['adittionalJob']) {
                $todo = '<span class="bagde badge-info ps-2 pe-2 rounded">N</span>';
                if ($array['todo'] == "Dringend") {
                    $todo = '<span class="bagde badge-danger ps-2 pe-2 rounded">D</span>';
                }
                echo '<div class="col-12 p-0 pb-3">
                    <div class="card p-0 mb-4 font-large shadow">
                        <div class="card-header ps-0">
                            <div class="row justify-content-end">';
                echo '<div class="col-12 p-0">';
                echo '<span class="h4 ps-2">Manueller Auftrag</span>';
                echo '</div>';
                echo '</div></div>
                        <div class="card-body p-2 overflow-auto">
                            <div class="table-responsive p-0">
                            <table class="p-0 m-0 table table-striped customized-normal laufschein" alt="">
                                <tr>
                                    <td class="small-viewport">' . $todo . ' Umfang: </td>
                                    <td class="ps-2">' . $array['umfang'] . '</td>
                                </tr>';
                echo '</table>
                            </div>
                        </div>
                        <div class="card-footer border-0">
                        <div class="row"><div class="col-12 text-center">';
                echo '<button type="button" class="btn btn-primary text-white border border-light done-manueller-auftrag-stapler rounded" alt="' . $index . '">Auftrag ausgeführt
                            </button>';
                echo '</div>
                        </div>
                        </div>
                    </div>';
            }
        }
    }
    public function alerts($dynamictext, $alertType = "success")
    {
        switch ($alertType) {
            case "success":
                $alertStyle = "alert-success";
                break;
            case "danger":
                $alertStyle = "alert-danger";
                break;
        }
        return '<div class="row"><div class="col-12 mt-2 mb-2 p-2 rounded text-center ' . $alertStyle . '">' . $dynamictext . '</div></div>';
    }
    public function chatConversation($path = "")
    {
        if (isset($_REQUEST['ajaxConversation'])) {
            $rfnum = $_REQUEST['rfnum'];
            $kfznum = $_REQUEST['Nummer'];
            $werknum = $_REQUEST['Werknummer'];
            $readDB = file_get_contents($path . "db/" . $werknum . "/conversation.json");
            if (!empty($readDB)) {
                $readJsonAsArray = json_decode($readDB, true);
                foreach ($readJsonAsArray as $key => $array) {
                    if ($array['rfnum'] == $rfnum && $array['Nummer'] == $kfznum) {
                        $return[$key] = $array;
                    }
                }
                foreach ($return as $key => $messages) {
                    if ($messages['gelesen'] != "gelesen") {
                        echo '<div class="col-12 alert-success rounded mb-2 message-item p-2" alt="' . $key . '">
                        <span class="font-italic small">' . date("d.m H:i", $messages['sendtime']) . '</span>
                        <h5>' . $messages['addnewmessage'] . ' ' . $messages['message-who'] . '</h5>
                        <p class="fs-4">' . $messages['message-text'] . '</p>
                        </div>';
                        echo "<embed src='assets/sound/ringing.mp3' style='opacity:0; height:.1em;'></embed>";
                    }
                }
            }
        }
    }
    public function messageReaded($path)
    {
        if (isset($_REQUEST['messagereaded'])) {
            $messageID = $_REQUEST['messagereaded'];
            $readDB = file_get_contents($path . "db/" . $_SESSION['werknummer'] . "/conversation.json");
            $arrays = json_decode($readDB, true);
            foreach ($arrays as $key => $array) {
                if ($key == $messageID) {
                    $array['gelesen'] = "gelesen";
                }
                $new[] = $array;
            }
            $entry = json_encode($new, JSON_PRETTY_PRINT);
            file_put_contents("../db/" . $_SESSION['werknummer'] . "/conversation.json", $entry);
        }
    }
    private function showMessages($rfnum)
    {
        if (!file_exists("../db/" . $_SESSION['werknummer'] . "/conversation.json")) {
            return "file does not exists";
        }
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
    public function checkStatusMonitor($status)
    {
        switch ($status) {
            case null:
            case 25:
                return "";
                break;
            case 50:
                return "alert-warning";
                break;
            case 75:
            case 751:
                return "alert-success";
                break;
            case 100:
            case 1001:
                return "alert-success";
                break;
        }
    }
    public function monitorPforte($getOrderList)
    {

        $lieferant = new Lieferant("ScannFolder");
        $col = "col-12";
        if (isset($_REQUEST['layoutVariante']) && $_REQUEST['layoutVariante'] == "custom") {
            $col = "col-md-6 col-lg-3";
        }
        echo '<div class="row mt-2 mb-2">';
        foreach ($getOrderList as $array):
            $scanner = str_replace("Nr.: ", "", $array['scanner']);
            $position = $this->geoPosition($scanner);
            if (!empty($array['LegitimationConfirm']) && $array['Status'] < 50):
                $open_dokumente = "<span class='d-block'>--</span>";
                $platz = "<span class='d-block'>--</span>";
                $dokumente = $lieferant->getLinkToLieferDokumenten($array['rfnum'], $array['Nummer']);

                if (!empty($dokumente)) {
                    $open_dokumente = '<a href="javascript:window.open(\'' . $this->scanFolder . $dokumente . '?t=' . time() . '\',\'Lieferdokumenten\',\'width=1000 height=850\')">LieferDoku</a>';
                }
                if (!empty($array['Platz'])) {
                    $platz = "<span class='d-block'>" . $array['Platz'] . "</span>";
                }
                echo '<div class="' . $col . ' p-1">';
                echo '<div class="card small p-0 border">
                <div class="card-header p-1 ' . $this->checkStatusMonitor($array['Status']) . ' row">';
                echo '<div class="col-9 p-0">';
                echo $this->showMessages($array['rfnum']);
                echo '</div>';
                echo '<div class="col-3 p-0 dropdown no-arrow text-end">
                    <a class="dropdown-toggle font-large" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-primary"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" style="">
                        <div class="dropdown-header">Actions</div>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="dropdown-item opensendbox" alt="' . $array['rfnum'] . '%20' . $array['Nummer'] . '%20' . $array['Firma'] . '%20' . $array['Werknummer'] . '" data-bs-toggle="modal" data-bs-target="#messageModal">
                            <span class="me-2"><i class="ti-location-arrow"></i></span> Nachricht an Fahrer
                        </a>
                        <a href="#" class="dropdown-item message-item" data="' . $_SESSION['weamanageruser'] . '&Zentrale" title="Nachricht senden" data-bs-toggle="modal" data-bs-target="#evochatModal">
                            <span class="me-2"><i class="ti-location-arrow"></i></span> Nachricht an Wareneingang
                        </a>
                        <a href="#" class="dropdown-item edit-rfnum" title="Datensatz bearbeiten" alt="' . $array['rfnum'] . '" data-bs-toggle="modal" data-bs-target="#editRFnum">
                        <span class="me-2"><i class="ti-pencil"></i></span> bearbeiten</a>
                    </div>
                    </div>';
                echo '</div>
                <div class="card-body p-2">
                <span class="badge badge-info float-end rounded font-large">' . $array['rfnum'] . '</span>
                <span class="d-block fs-4">' . $array['Nummer'] . '</span>';
                echo '<span class="d-block">' . $array['Firma'] . '</span>';
                echo $open_dokumente;
                echo $platz;
                echo '<span class="d-block small"><img src="assets/img/flage' . $array['Sprache'] . '.JPG" class="img-fluid rounded d-inline me-2" style="width:10%"> ' . $array['Anmeldung'] . '</span>
                <span class="d-block small">Scanner ' . $array['scanner'] . '</span>';
                echo '<div class="card-footer pt-1 p-0 text-end">
                <span class="ms-2"></span>
                    <button class="btn btn-sm p-1 ps-2 pe-2 btn-info text-white checkScannerOnline" alt="' . $scanner . '">check</button>
                    <button class="btn btn-sm p-1 ps-2 pe-2 btn-info text-light open-map-iframe" data="https://maps.google.com/maps?q=' . $position[0]['latitude'] . ',' . $position[0]['longtitude'] . '&z=17&output=embed&t=k&iwloc=addr">Karte</button>
                </div>
                </div>
            </div>';
                echo '</div>';
            endif;
        endforeach;
        echo '</div>';
    }
    public function monitorSchichtLeiter($getOrderList)
    {
        $leftCol = "col-3";
        if ($_SESSION['INUMMER'] == 1) {
            $leftCol = "col-12";
        }
        echo '<div class="row justify-content-center mt-2 mb-2">';
        foreach ($getOrderList as $array):
            $stapler = null;
            if ($array['Status'] > 25 && $array['Status'] < 100):
                $stapler = $array['Stapler']['BMI-Nummer'];
                echo '<div class="card p-0 border mb-2 shadow ' . $leftCol . ' ">
                    <div class="card-header pt-1 pb-1 font-large ' . $this->checkStatusMonitor($array['Status']) . '">';
                if (empty($stapler)):
                    echo '<button type="button" alt="' . $array['rfnum'] . ':' . $array['Nummer'] . '" data-bs-toggle="modal" data-bs-target="#setSchipperModal" class="set-forkbully btn btn-primary p-2 font-style-normal text-white">
                    <i class="ti-target"></i> Stapler zuweisen
                    </button>';
                endif;
                if (!empty($stapler)):
                    echo '<span class="badge badge-success pictureviwever-show pointer" data-bs-toggle="modal" data-bs-target="#pictureviwever" alt="db/' . $_SESSION['werknummer'] . "/bmi/" . $array['Stapler']['BMI-Bild'] . '" src="db/' . $_SESSION['werknummer'] . "/bmi/TN" . $array['Stapler']['BMI-Bild'] . '">' . $stapler . '</span>';
                endif;
                echo '</div>
                    <div class="card-body">
                    <span class="badge badge-light float-end text-black-50 border font-large">' . $array['rfnum'] . '</span>
                    <span class="d-block">' . $array['Nummer'] . '</span>
                    <span class="d-block">' . $array['Firma'] . '</span>
                    <span class="d-block">' . $array['Platz'] . '</span>
                    <span class="d-block">' . $array['Ladung'] . '</span>
                    <span class="d-block">' . $array['ladung_beschreibung'] . '</span>
                    </div>
                </div>';
            endif;
        endforeach;
        //echo '</ul>
        echo '</div>';
    }
    public function autoChangeStatusWerksverkehr($arrays, $limit = 60)
    {
        $range = time() - $limit;
        foreach ($arrays as $array) {
            if ($array['Prozessname'] == "Werksverkehr" && $array['Status'] == 100) {
                $arr[$array['rfnum']] = $array['Tstamp-Abfertigung'];
            }
        }
        foreach ($arr as $rfnum => $value) {
            if ($value < $range) {
                $liteDB = new Sqlite($_SESSION['werknummer']);
                $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
                $result = $liteDB->sqliteSelect($q);
                $readJsonAsArray = json_decode($result[0]['object'], true);
                $readJsonAsArray['Status'] = 120;
                $this->updateTraffic($rfnum, $readJsonAsArray, "../");
            }
        }
    }
    public function werksVerkehrCounter($path = "")
    {
        require_once 'Controller.php';
        $o = new Controller();
        echo  $o->maxIDinOrerList($path) + 1;
    }
    public function werksverkehrBox($path = "")
    {
        $arrays = $this->readTraffic($path);
        foreach ($arrays as $array) {
            if ($array['Status'] == 75) {
                echo "<div class='alert alert-info p-1 mb-2 rounded row border shadow'>
                <div class='col-10 p-0'>
                " . $array['Firma'] . "
                <span class='d-block smaller'>" . date("d.m. H:i", $array['timestamp']) . "</span>
                </div>
                <div class='col-2 p-0 text-end'>
                <span class='badge badge-info rounded'>" . $array['rfnum'] . "</span></div>
                </div>";
            }
        }
    }
    public function autoSuggesting($keyword)
    {
        require_once 'connect.php';
        $o = new connect();
        $q = "SELECT TOP 500 object_runleaf FROM werk_" . $_SESSION['werknummer'] . "";
        $results = $o->select($q);
        if (empty($results)) {
            echo "[{}]";
        }
        foreach ($results as $result) {
            $toArray = json_decode($result['object_runleaf'], true);
            if (!empty($toArray[$keyword])) {
                $temp[$toArray[$keyword]] = $toArray[$keyword];
            }
        }
        foreach ($temp as $value) {
            $arr[] = $value;
        }
        echo json_encode($arr);
    }
    public function startSound($rfnum, $path = "")
    {
        $arrays = $this->readTraffic($path);
        $sound = new Sounds("assets/sound/");
        $status = null;
        foreach ($arrays as $array) {
            if ($array['rfnum'] == $rfnum) {
                $status = $array['Status'];
            }
        }
        if (isset($_SESSION['alarm_stopped']) && $_SESSION['alarm_stopped'] == 0) {
            if ($status == 25 || $status == 50) {
                $sound->setAlarm("ringing.mp3");
                echo $sound->addAudio();
                exit;
            }
        }
    }
    public function startSoundByCall($rfnum, $path = "")
    {
        $arrays = $this->readTraffic($path);
        $liteDB = new Sqlite($_SESSION['werknummer']);
        $sound = new Sounds("assets/sound/");
        $alarm = null;
        foreach ($arrays as $array) {
            if ($array['rfnum'] == $rfnum) {
                $alarm = $array['alarm'];
                $array['alarm'] = "stopped";
                $toJson = json_encode($array);
                $q = "UPDATE traffic SET object='$toJson' WHERE rfnum=" . $rfnum . "";
                $liteDB->sqliteQuery($q);
            }
        }

        if ($alarm == "callByClick") {
            $sound->setAlarm("pinging.mp3");
            echo $sound->addAudio("call-by-click");

            exit;
        }
    }
    //zollsound
    public function startSoundZoll($rfnum, $path = "")
    {
        if (isset($_SESSION['zoll_alarm_stopped']) && $_SESSION['zoll_alarm_stopped'] == 0) {
            $arrays = $this->readTraffic($path);
            $status = null;
            foreach ($arrays as $array) {
                if ($array['rfnum'] == $rfnum) {
                    $status = $array['Status'];
                    if ($status < 50 && !empty($array['Zollabfertigung'])) {
                        $sound = new Sounds("assets/sound/");
                        $sound->setAlarm("ringing.mp3");
                        echo $sound->addAudio();
                        exit;
                    }
                }
            }
        }
    }
    public function checkQRCodeBeforeRegester($path)
    {
        extract($_REQUEST);
        $readDB = file_get_contents($path . "db/lieferanten.json");
        $filter = str_replace("saved-", "", $requestNummer);
        if (!empty($readDB)) {
            $readJson = json_decode($readDB, true);
            foreach ($readJson as $lieferant) {
                if ($lieferant['modifyNummer'] == $filter) {
                    header("location:../wea?requestNummer=$filter");
                    exit;
                }
            }
        }
        header("location:../wea?requestNummer=$filter&checkcode=notfound");
    }
    private function saveCanvasImage($imageData, $imageName, $path)
    {
        $imageName = str_replace("|", "_", $imageName);
        list($type, $data) = explode(';', $imageData);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        return file_put_contents($path . $imageName, $data);
    }
    public function changeRessourseStatus($pfad)
    {
        extract($_REQUEST);
        $readDB = json_decode(file_get_contents($pfad . "db/" . $_SESSION['werknummer'] . "/bmi.json"), true);
        $resourse = $_SESSION['weamanageruser'];
        foreach ($readDB as $key => $array) {
            if ($array['BMI-Nummer'] == $resourse) {
                $readDB[$key]['available'] = $changeRessourseStatus;
            }
        }
        $toJson = json_encode($readDB, JSON_PRETTY_PRINT);
        file_put_contents($pfad . "db/" . $_SESSION['werknummer'] . "/bmi.json", $toJson);
        echo $changeRessourseStatus;
    }
    public function checkRessourseStatus($resourse, $pfad = "")
    {
        $readDB = json_decode(file_get_contents($pfad . "db/" . $_SESSION['werknummer'] . "/bmi.json"), true);
        $resourse = $_SESSION['weamanageruser'];
        foreach ($readDB as $key => $array) {
            if ($array['BMI-Nummer'] == $resourse) {
                if ($array['available'] == "notavailable") {
                    return "<span class='btn btn-danger-custom me-2'></span> <i class='ti-na mx-0'></i>";
                }
                if (empty($array['available']) || $array['available'] == "available") {
                    return "<span class='btn btn-success-custom me-2'></span> <i class='ti-user mx-0'></i>";
                }
            }
        }
    }
    public function ajaxSaveCanvasImage()
    {
        extract($_REQUEST);
        $knznummer = str_replace("|", "_", $knznummer);
        $bild = "lf_" . $rfnum . "_" . str_replace(" ", "", $knznummer) . "_" . time() . ".jpg";
        $imagePath = "../db/" . $_SESSION['werknummer'] . "/img_temp/";

        $liteDB = new Sqlite($_SESSION['werknummer']);
        $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
        $result = $liteDB->sqliteSelect($q);
        $readJsonAsArray = json_decode($result[0]['object'], true);
        $readJsonAsArray['Lieferschein'] = $bild;
        $this->updateTraffic($rfnum, $readJsonAsArray, "../");
        echo $this->saveCanvasImage($lieferschein, $bild, $imagePath);
    }
    public function checkLegitimationConfirmation($path = "")
    {
        if (isset($_REQUEST['checkLegitimationConfirmation'])) {
            extract($_REQUEST);
            $liteDB = new Sqlite($_SESSION['werknummer']);
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            if (empty($readJsonAsArray['LegitimationConfirm'])) {
                echo '{"legitimation":"not confirmed","document":"' . $readJsonAsArray['Legitimation'] . '"}';
                exit;
            }
        }
    }
    public function setGeolocation($path = "")
    {
        extract($_REQUEST);
        $x = substr(str_replace(".", "", $coordinateX), 0, -1);
        $y = substr(str_replace(".", "", $coordinateY), 0, -1);
        $lenghX = strlen($x);
        $lenghY = strlen($y);
        if ($lenghX < 8) {
            $x = $x . "0";
        }
        if ($lenghY < 8) {
            $y = $y . "0";
        }
        $lenghY = strlen($y);
        $liteDB = new Sqlite($_SESSION['werknummer'], $path);

        $q = "SELECT * FROM scanner";
        $arrays = $liteDB->sqliteSelect($q);

        foreach ($arrays as $key => $array) {

            if ($scannerIP == $array['IP']) {
                $arrays[$key]['coordXlatitude'] = $x;
                $arrays[$key]['coordYlongtitude'] = $y;
                $arrays[$key]['latitude'] = $coordinateX;
                $arrays[$key]['longtitude'] = $coordinateY;
                // echo "X ".$coordinateX."<br>Y ".$coordinateY;

                $q = "UPDATE scanner SET coordXlatitude='$x',coordYlongtitude='$y',latitude='$coordinateX',longtitude='$coordinateY' WHERE IP='" . $array['IP'] . "'";
                $liteDB->sqliteQuery($q);

                // $toJSON = json_encode($arrays,JSON_PRETTY_PRINT);
                // file_put_contents($path."db/".$_SESSION['werknummer']."/scanner.json",$toJSON);
                exit;
            }
        }
    }
}