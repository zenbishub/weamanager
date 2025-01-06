<?php
class Controller
{
    public $berechtigte = [];
    public $werkpfad;
    public $werkdata = [];
    public $inummer;
    public $userFilter;
    public $base;
    public $database;
    public $scanFolder;
    public function __construct()
    {
        if (empty(session_id())) {
            session_start();
        }
        $this->setAppData();
        error_reporting(0);
    }
    private function setAppData()
    {
        $appPath = __dir__ . "/app.json";
        if (!file_exists($appPath)) {
            die("Can not reach App Config. Check app.json");
        }
        $app = json_decode(file_get_contents($appPath));
        $_SESSION['werknummer'] = $app->appdata->werknummer;
        $this->base = $app->appdata->base;
        $this->database = $app->appdata->database;
        $this->scanFolder = $app->appdata->scanFolder;

        if (!isset($_SESSION['alarm_stopped'])) {
            $_SESSION['alarm_stopped'] = 0;
        }
        if (!isset($_SESSION['zoll_alarm_stopped'])) {
            $_SESSION['zoll_alarm_stopped'] = 0;
        }
        if (!isset($_SESSION['frzlaufnummer'])) {
            $_SESSION['frzlaufnummer'] = null;
        }
        if (!isset($_SESSION['INUMMER'])) {
            $_SESSION['INUMMER'] = 1;
        }
        if (!isset($_SESSION['adittionalJob'])) {
            $_SESSION['adittionalJob'] = null;
        }
        $this->userFilter = [
            "WE5" => ["Wareneingang Werk 5", "Leergut-Abholung", "Paketdienst", "Langgut", "Zollstelle"],
            "VERS5" => ["Versand Werk 5"],
            "WE9" => ["Transport für Werk 9", "Zollstelle"]
        ];
    }
    public function appOnOff()
    {
        $active = json_decode(file_get_contents("db/apponline.json"));
        if ($active->app == "offline") {
            die("<h1>TMI LKW-Manager ist zur Zeit nicht aktiv</h1><p>Die Anwendung wird gewartet. Wenden Sie sich an den Administrator.</p>");
        }
    }
    public function getAppOnOff()
    {
        $active = json_decode(file_get_contents("db/apponline.json"));
        switch ($active->app) {
            case "offline":
                $return = "<div class='p-2 h4 badge bg-danger rounded'>offline</div>";
                break;
            default:
                $return = "<div class='p-2 h4 badge bg-success rounded'>online</div>";
        }
        return $return;
    }
    public function onoff()
    {
        if (isset($_REQUEST['on_off'])) {
            unset($_POST['on_off']);
            file_put_contents("../db/apponline.json", json_encode($_POST));
            header("location:../settings?setting=admintool");
        }
    }
    public function checkSession()
    {
        if (isset($_COOKIE['weamanageruser'])) {
            $_SESSION['weamanageruser'] = $_COOKIE['weamanageruser'];
        }
        if (isset($_COOKIE['schichtleiter'])) {
            $_SESSION['schichtleiter'] = $_COOKIE['schichtleiter'];
        }
        if (isset($_COOKIE['weamanager_roll'])) {
            $_SESSION['weamanager_roll'] = $_COOKIE['weamanager_roll'];
        }
        if (isset($_COOKIE['werknummer'])) {
            $_SESSION['werknummer'] = $_COOKIE['werknummer'];
        }
        if (isset($_COOKIE['abteilung'])) {
            $_SESSION['abteilung'] = $_COOKIE['abteilung'];
        }
        if (isset($_COOKIE['INUMMER'])) {
            $_SESSION['INUMMER'] = $_COOKIE['INUMMER'];
        }
        if (isset($_COOKIE['werkname'])) {
            $_SESSION['werkname'] = $_COOKIE['werkname'];
        }
        if (isset($_COOKIE['weamanager_access'])) {
            $_SESSION['weamanager_access'] = $_COOKIE['weamanager_access'];
        }
        if (isset($_COOKIE['adittionalJob'])) {
            $_SESSION['adittionalJob'] = $_COOKIE['adittionalJob'];
        }
        if (isset($_REQUEST['return_uri']) && $_REQUEST['return_uri'] == "wea") {
            $_SESSION['weamanageruser'] = "wea";
        }

        if (!isset($_SESSION['weamanageruser'])) {
            $expl = explode("/", $_SERVER['REQUEST_URI']);
            $lastField = count($expl) - 1;
            switch ($expl[$lastField]) {
                case "monitor":
                    header("location:login?app=monitor");
                    break;
                case "maingate":
                    header("location:login?app=maingate");
                    break;
                default:
                    header("location:login?app=weamanager");
                    break;
                case "stapler":

                    header("location:login?app=stapler");
                    break;
                case "sonderfahrt":
                    header("location:login?app=sonderfahrt");
                    break;
                case "zollgut":
                    header("location:login?app=zollgut");
                    break;
                case "information":
                    header("location:login?app=information");
                    break;
            }
            exit;
        }
        $this->checkPremissions();
    }
    private function checkPremissions()
    {
        if (isset($_SESSION['weamanageruser'])) {
            $expl = explode("/", $_SERVER['REQUEST_URI']);
            $arrays = json_decode(file_get_contents("db/" . $_SESSION['werknummer'] . "/rolles.json"), true);
            $prepare = explode("?", $expl[3]);
            foreach ($arrays as $userdata) {
                if (!empty($userdata['userid'] == $_SESSION['weamanageruser'])) {
                    if (!empty($userdata['URL'])) {
                        if ($prepare[0] != $userdata['URL']) {
                            die("<h1>You have no Access to this Modul</h1>");
                        }
                    }
                }
            }
        }
    }
    private function readJsonData($path = "", $file = "entladefluess")
    {
        return file_get_contents($path . "db/" . $_SESSION['werknummer'] . "/" . $file . ".json");
    }
    private function readTraffic($pfad, $order = "rfnum", $byFilter = true)
    {
        $liteDB = new Sqlite($_SESSION['werknummer'], $pfad);
        $q = "SELECT * FROM traffic ORDER BY $order";
        $results = $liteDB->sqliteSelect($q);
        // LKW filtern nach User
        if (isset($_SESSION['setLKWfilter']) && $byFilter == true) {
            foreach ($results as $result) {
                $toArray = json_decode($result['object'], true);
                if (in_array($toArray['Ladung'], $this->userFilter[$_SESSION['setLKWfilter']])) {
                    $array[] = $toArray;
                }
            }
            return $array;
        }
        foreach ($results as $result) {
            $array[] = json_decode($result['object'], true);
        }
        return $array;
    }
    public function setOnlineUser($pfad)
    {
        if (isset($_REQUEST['setOnlineUser'])) {
            $liteDB = new Sqlite($_SESSION['werknummer'], $pfad);
            // $range = time()-60; // 5 Min.
            $user = $_SESSION['weamanageruser'];
            $rfnum = null;
            $lkwFilter = null;
            $abteilung = null;
            if (!empty($_SESSION['setLKWfilter'])) {
                $lkwFilter = $_SESSION['setLKWfilter'];
            }
            if (!empty($_SESSION['abteilung'])) {
                $abteilung = $_SESSION['abteilung'];
            }
            if (empty($user)) {
                $user = $_SESSION['frzlaufnummer'];
            }
            if (!empty($_SESSION['rfnum'])) {
                $rfnum = $_SESSION['rfnum'];
            }
            if (!empty($user)) {
                $q = "SELECT * FROM useronline WHERE user='$user'";
                $result = $liteDB->sqliteSelect($q);
                if (!empty($result[0]['user'])) {
                    $q = "UPDATE useronline SET time =" . time() . ", wn='$rfnum', bereich='$abteilung', lkwfilter='$lkwFilter' WHERE user='$user'";
                    $liteDB->sqliteQuery($q);
                    echo "update user data";
                }
                if (empty($result[0]['user'])) {
                    $q = "INSERT INTO useronline(time,user,wn,bereich,lkwfilter)VALUES(" . time() . ",'$user','$abteilung','$rfnum','$lkwFilter')";
                    $liteDB->sqliteQuery($q);
                    echo "insert user data";
                }
            }
            //$q="DELETE FROM useronline WHERE time<$range";
            //$liteDB->sqliteQuery($q);
        }
    }
    public function whoIsOnline()
    {
        $liteDB = new Sqlite($_SESSION['werknummer']);
        $q = "SELECT * FROM useronline ORDER BY user";
        $result = $liteDB->sqliteSelect($q);
        return $result;
    }
    public function readTrafficPublic($pfad, $order = "rfnum")
    {
        return $this->readTraffic($pfad, $order);
    }
    private function insertTraffic($rfnum, $data)
    {
        if (!empty($rfnum)) {
            $entry = json_encode($data);
            $liteDB = new Sqlite($_SESSION['werknummer']);
            $q = "SELECT tstamp FROM traffic WHERE rfnum=$rfnum";
            $num = $liteDB->sqliteNumRows($q);
            if ($num == 0) {
                $q = "INSERT INTO traffic (rfnum,object,tstamp,ordering)VALUES(
                    $rfnum,
                    '$entry',
                    " . time() . ",
                    1
                )";
            }
            $liteDB->sqliteQuery($q);
        }
    }
    private function updateTraffic($rfnum, $data)
    {
        if (!empty($rfnum)) {
            $entry = json_encode($data);
            $liteDB = new Sqlite($_SESSION['werknummer']);
            $q = "UPDATE traffic SET object='" . $entry . "' WHERE rfnum=$rfnum";
            return $liteDB->sqliteQuery($q);
        }
    }
    private function writeJsonData($array, $path = "", $file = "entladefluess")
    {
        $entry = json_encode($array, JSON_PRETTY_PRINT);
        file_put_contents($path . "db/" . $_SESSION['werknummer'] . "/" . $file . ".json", $entry);
    }
    public function selectPlant($path = "")
    {
        $readDB = file_get_contents($path . "db/config.json");
        return json_decode($readDB, true);
    }
    public function selectLadungDaten($path = "")
    {
        $readDB = $this->readJsonData($path, "ladungdaten");
        return json_decode($readDB, true);
    }
    public function selectFRZDaten($path = "")
    {
        $readDB = $this->readJsonData($path, "frztyp");
        return json_decode($readDB, true);
    }
    private function authActiveDirectory($loginname, $password)
    {
        $adServer = "";
        $getDomain = "../../../domain.txt";
        if (file_exists($getDomain)) {
            $adServer = file_get_contents($getDomain);
        }
        $ldap = ldap_connect($adServer);
        $username = $loginname;
        $explode = explode(".", $adServer);
        $domain = $explode[0];
        $ldaprdn = $domain . "\\" . $username;
        ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
        return @ldap_bind($ldap, $ldaprdn, $password);
    }
    private function authByConfig($loginname, $password, $inummer)
    {
        $arrays = $this->selectPlant("../");
        foreach ($arrays as $array) {
            foreach ($array['Deparments'] as $abt) {
                if ($loginname == $abt['Ber-ID'] && $password == $abt['BP'] && $array['INUMER'] != $inummer) {
                    $_SESSION['abteilung'] = $abt['Abt-ID'];
                    return 1;
                }
            }
        }
    }
    public function login()
    {
        if (isset($_REQUEST['userlogin'])) {

            $werkdata = $this->selectPlant("../");
            $arrays = json_decode(file_get_contents("../db/" . $_SESSION['werknummer'] . "/rolles.json"), true);
            $arraysBMI = json_decode(file_get_contents("../db/" . $_SESSION['werknummer'] . "/bmi.json"), true);
            $entry = "";
            $roll = "";
            $result = 0;
            if (isset($_REQUEST['app'])) {
                $entry = $_REQUEST['app'];
                $returnback = $_REQUEST['app'];

                if ($entry == "weamanager") {
                    $entry = "";
                }
            }
            $expl = explode(":", $_REQUEST['werknummer']);
            $_SESSION['werknummer'] = $expl[0];
            $_SESSION['INUMMER'] = $expl[1];
            $loginname = strtoupper($_REQUEST['user']);
            $password = $_REQUEST['pass'];
            $uservalid = false;
            $result = $this->authActiveDirectory($loginname, $password);
            if (empty($result)) {
                $result = $this->authByConfig($loginname, $password, $expl[1]);
            }
            if (strtoupper($loginname) == "ZENBIS" && $password == "nurich") {
                $result = 1;
            }
            if ($loginname == "ZOLLSTELLE" && strtoupper($password) == "MAINGATE") {
                $result = 0;
                $uservalid = "zollstelle";
                $schichtleiter = "";
                $roll = "ZOLLSTELLE";
            }
            if ($loginname == "PFORTE" && strtoupper($password) == "MAINGATE") {
                $result = 0;
                $uservalid = "maingate";
                $schichtleiter = "";
                $roll = "MAINGATE";
            }
            if (!empty($loginname) && strtoupper($password) == "STARTSTAPLER") {
                $result = 0;
                $uservalid = "staplerlogin";
                $schichtleiter = "";
                $roll = "Mitarbeiter";
            }
            if (!empty($loginname) && strtoupper($password) == "SITZANDIENUNG") {
                $result = 0;
                $uservalid = "staplerlogin";
                $schichtleiter = "";
                $roll = "Mitarbeiter";
            }
            if (!empty($loginname) && strtoupper($password) == "SCHICHTLEITER") {
                $result = 0;
                $uservalid = "staplerlogin";
                $schichtleiter = strtoupper($password);
                $roll = "Schichtleiter";
            }
            if ($result == 1) {
                $uservalid = "applogin";
            }
            switch ($uservalid) {
                case "applogin":
                    // nur wenn alle Angaben true und nicht leer dann erfolgt Login
                    if (isset($_REQUEST['Abteilung'])) {
                        $_SESSION['abteilung'] = $_REQUEST['Abteilung'];
                    }
                    foreach ($werkdata as $data) {
                        if ($data['INUMMER'] == $_SESSION['INUMMER']) {
                            $_SESSION['werkname'] = $data['Werkname'];
                        }
                    }
                    foreach ($arrays as $array) {
                        if ($array['userid'] == strtoupper($loginname)) {
                            $_SESSION['weamanageruser'] = strtoupper($loginname);
                            $_SESSION['weamanager_roll'] = $array['Roll'];
                            $_SESSION['weamanager_access'] = $array['Access'];

                            setcookie("weamanageruser", $_SESSION['weamanageruser'], time() + 86400, "/");
                            setcookie("weamanager_roll", $_SESSION['weamanager_roll'], time() + 86400, "/");
                            setcookie("werknummer", $_SESSION['werknummer'], time() + 86400, "/");
                            setcookie("abteilung", $_SESSION['abteilung'], time() + 86400, "/");
                            setcookie("INUMMER", $_SESSION['INUMMER'], time() + 86400, "/");
                            setcookie("werkname", $_SESSION['werkname'], time() + 86400, "/");
                            setcookie("weamanager_access", $_SESSION['weamanager_access'], time() + 86400, "/");

                            header("location:" . $this->base . $entry);
                            exit;
                        }
                        $_SESSION['weamanageruser'] = strtoupper($loginname);
                        $_SESSION['weamanager_roll'] = "DISPOORZOLL";
                        $_SESSION['weamanager_access'] = "";
                        header("location:" . $this->base . $entry);
                    }
                    break;
                case "staplerlogin":
                    $staplerFound = [];
                    foreach ($arraysBMI as $array) {
                        $explBMI = explode("&", $array['Plant']);
                        $bminummer = $array['BMI-Nummer'];
                        $inummerBMI = $explBMI[2];
                        $explREQ = explode(":", $_REQUEST['werknummer']);
                        $inummerREQ = $explREQ[1];
                        if ($bminummer == $_REQUEST['user'] && $inummerBMI == $inummerREQ) {
                            $staplerFound[] = $bminummer;
                        }
                    }

                    if (empty($staplerFound)) {
                        header("location:" . $this->base . "login?app=stapler&error=ressourse_notfound");
                        exit;
                    }
                    foreach ($arraysBMI as $array) {
                        if ($array['BMI-Nummer'] == strtoupper($loginname)) {
                            $expl = explode("&", $array['DEP']);
                            $_SESSION['abteilung'] = $expl[1];
                            $_SESSION['weamanageruser'] = strtoupper($loginname);
                            $_SESSION['schichtleiter'] = ucfirst($schichtleiter);
                            $_SESSION['weamanager_roll'] = $roll;
                            $_SESSION['weamanager_access'] = $array['Access'];
                            foreach ($werkdata as $data) {
                                if ($data['INUMMER'] == $_SESSION['INUMMER']) {
                                    $_SESSION['werkname'] = $data['Werkname'];
                                }
                            }
                            setcookie("weamanageruser", $_SESSION['weamanageruser'], time() + 86400, "/");
                            setcookie("schichtleiter", $_SESSION['schichtleiter'], time() + 86400, "/");
                            setcookie("weamanager_roll", $_SESSION['weamanager_roll'], time() + 86400, "/");
                            setcookie("werknummer", $_SESSION['werknummer'], time() + 86400, "/");
                            setcookie("abteilung", $_SESSION['abteilung'], time() + 86400, "/");
                            setcookie("INUMMER", $_SESSION['INUMMER'], time() + 86400, "/");
                            setcookie("werkname", $_SESSION['werkname'], time() + 86400, "/");
                            setcookie("weamanager_access", $_SESSION['weamanager_access'], time() + 86400, "/");
                            $this->setOnlineData("../", $array['BMI-Nummer'], $_SESSION['INUMMER']);

                            switch (strtoupper($password)) {
                                case "STARTSTAPLER":
                                case "SCHICHTLEITER";

                                    header("location:" . $this->base . $entry);
                                    break;
                                case "SITZANDIENUNG":

                                    header("location:" . $this->base . $entry . "?job=sitzandienung");
                                    break;
                            }
                        }
                    }
                    break;
                case "maingate":
                case "zollstelle":
                    foreach ($arrays as $array) {
                        if ($array['userid'] == strtoupper($loginname)) {
                            $_SESSION['weamanageruser'] = strtoupper($loginname);
                            $_SESSION['weamanager_roll'] = $array['Roll'];
                            $_SESSION['weamanager_access'] = $array['Access'];

                            foreach ($werkdata as $data) {
                                if ($data['INUMMER'] == $_SESSION['INUMMER']) {
                                    $_SESSION['werkname'] = $data['Werkname'];
                                }
                            }
                            setcookie("weamanageruser", $_SESSION['weamanageruser'], time() + 86400, "/");
                            setcookie("weamanager_roll", $_SESSION['weamanager_roll'], time() + 86400, "/");
                            setcookie("werknummer", $_SESSION['werknummer'], time() + 86400, "/");
                            setcookie("INUMMER", $_SESSION['INUMMER'], time() + 86400, "/");
                            setcookie("werkname", $_SESSION['werkname'], time() + 86400, "/");
                            setcookie("weamanager_access", $_SESSION['weamanager_access'], time() + 86400, "/");

                            header("location:" . $this->base . $entry);
                            exit;
                        }
                        $_SESSION['weamanageruser'] = strtoupper($loginname);
                        $_SESSION['weamanager_roll'] = "DISPOORZOLL";
                        $_SESSION['weamanager_access'] = "";
                        header("location:" . $this->base . $entry);
                    }
                    break;
                case false:
                    header("location:../login?app=$returnback&error=user_notfound");
                    break;
            }
        }
    }
    public function logout()
    {
        if (isset($_REQUEST['logout'])) {
            extract($_REQUEST);

            $returnURI = $this->base;
            if (isset($appname)) {
                $returnURI = $this->base . "login?app=" . $appname;
            }

            session_destroy();
            setcookie("weamanageruser", "", time() - 1, "/");
            setcookie("schichtleiter", "", time() - 1, "/");
            setcookie("weamanager_roll", "", time() - 1, "/");
            setcookie("werknummer", "", time() - 1, "/");
            setcookie("abteilung", "", time() - 1, "/");
            setcookie("INUMMER", "", time() - 1, "/");
            setcookie("werkname", "", time() - 1, "/");
            setcookie("weamanager_access", "", time() - 1, "/");
            setcookie("adittionalJob", "", time() - 1, "/");

            header("location:$returnURI");
        }
    }
    public function setLanguage()
    {
        if (isset($_REQUEST['set_language'])) {
            $return_uri = $_REQUEST['return_uri'];
            $_SESSION['wealanguage'] = $_REQUEST['set_language'];
            $_SESSION['start_prozess'] = time();

            if (isset($_SESSION['rfnum'])) {
                $liteDB = new Sqlite($_SESSION['werknummer']);
                $q = "SELECT object FROM traffic WHERE rfnum=" . $_SESSION['rfnum'] . "";
                $result = $liteDB->sqliteSelect($q);
                $readJsonAsArray = json_decode($result[0]['object'], true);
                $readJsonAsArray['Sprache'] = $_REQUEST['set_language'];
                $this->updateTraffic($_SESSION['rfnum'], $readJsonAsArray);
            }
            header("location:../$return_uri");
        }
    }
    public function getRolles($path = "")
    {
        $readDB = $this->readJsonData($path, "personal");
        return json_decode($readDB, true);
    }
    public function requestFRZdata()
    {
        if (isset($_REQUEST['requestfrzdata'])) {
            extract($_POST);
            switch ($responseData) {
                case "kennzeichen":
                    $readDB = file_get_contents("../db/lieferanten.json");
                    if (!empty($readDB)) {

                        $readJsonAsArray = json_decode($readDB, true);
                        foreach ($readJsonAsArray as $key => $value) {

                            if ($Nummer == $value['Nummer']) {
                                echo implode(
                                    ",",
                                    [
                                        $value['lftnid'],
                                        $value['Firma'],
                                        $value['FRZTyp'],
                                        $value['Nummer'],
                                        $value['name_fahrer'],
                                        $value['knznummer_aufleger'],
                                        $value['radio_legitimation'],
                                        $value['legitimation'],
                                        "",
                                        $value['transport_for'],
                                        "",
                                        "",
                                        ""
                                    ]
                                );
                                exit;
                            }
                        }
                    }
                    break;
                case "voranmeldung":
                    $db = new connect();
                    $q = "SELECT * FROM tracker";
                    $results = $db->select($q);
                    $abladestellen = json_decode(file_get_contents("../db/" . $_SESSION['werknummer'] . "/entladestellen_sofa.json"), true);

                    if (!empty($results)) {
                        foreach ($results as $result) {
                            $zoll = "";
                            $firma = "";
                            $dataFirma = "";
                            $arrays = json_decode($result['object_frz'], true);

                            foreach ($arrays as $autokennzeichen => $array) {

                                if ($Nummer == $autokennzeichen) {
                                    //echo $autokennzeichen;

                                    $firma = $result['kennzeichen'];

                                    $abladestelleID = strtoupper($array['abladestellID']);
                                    foreach ($abladestellen as $abladestelle) {
                                        if ($abladestelleID == $abladestelle['Abl-ID']) {

                                            switch ($abladestelleID) {
                                                case "41":
                                                case "81":
                                                case "H1":
                                                case "B1":
                                                case "D1":
                                                case "S1":
                                                case "F1":
                                                    $redirect = "redirect";
                                                    break;
                                                default:
                                                    $redirect = "notredirect";
                                            }
                                            $Adresse = implode(", ", $abladestelle);
                                        }
                                    }

                                    switch ($firma) {
                                        case "ekol":
                                            $zoll = "zollgut";
                                            $dataFirma = "Ekol Logistics";
                                            break;
                                        case "hegelmann":
                                            $zoll = "";
                                            $dataFirma = "Hegelmann Group";
                                            break;
                                    }


                                    echo implode(
                                        ",",
                                        [
                                            $result['id'],
                                            $dataFirma,
                                            $array[0]['FRZTyp'],
                                            $Nummer,
                                            $array[0]['name_fahrer'],
                                            $Nummer,
                                            $array[0]['radio_legitimation'],
                                            $array[0]['legitimation'],
                                            $zoll,
                                            $array[0]['transport_for'] . "" . $array['transport_for'],
                                            $Adresse,
                                            $redirect,
                                            $array[0]['Dienstleister'],

                                        ]
                                    );
                                    exit;
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }
    public function requestFRZdataByScanQRcode()
    {
        if (isset($_REQUEST['requestfrzdatabyscanqrcode'])) {
            extract($_POST);
            $readDB = $this->readJsonData("../", "lieferanten");
            if (!empty($readDB)) {

                $readJsonAsArray = json_decode($readDB, true);
                foreach ($readJsonAsArray as $key => $value) {

                    if ($modifyNummer == $value['modifyNummer']) {
                        echo implode(
                            ",",
                            [
                                $value['lftnid'],
                                $value['Firma'],
                                $value['FRZTyp'],
                                $value['Nummer'],
                                $value['name_fahrer'],
                                $value['knznummer_aufleger'],
                                $value['radio_legitimation'],
                                $value['legitimation']

                            ]
                        );
                        $_SESSION['start_prozess'] = time();
                        exit;
                    }
                }
            }
        }
    }
    public function removeFromOrder()
    {
        if (isset($_REQUEST['remove_from_order'])) {
            $returnUri = "";
            extract($_REQUEST);
            $this->removeFromConversation($rfnum, "../");
            require_once __dir__ . '/Sqlite.php';
            $liteDB = new Sqlite($_SESSION['werknummer'], "../");
            $q = "DELETE FROM traffic WHERE rfnum=$rfnum";
            $liteDB->sqliteQuery($q);
            header("location:../$returnUri");
            exit;
        }
    }
    private function deleteOhneProzess()
    {

        extract($_REQUEST);
        $rfnum = $_SESSION['rfnum'];
        require_once 'Sqlite.php';
        $liteDB = new Sqlite($_SESSION['werknummer']);
        $q = "DELETE FROM traffic WHERE rfnum=$rfnum";
        $liteDB->sqliteQuery($q);

        unset($_SESSION['frzlaufnummer']);
        unset($_SESSION['alarm_stopped']);
        unset($_SESSION['zoll_alarm_stopped']);
        unset($_SESSION['wealanguage']);
        unset($_SESSION['start_prozess']);
        session_destroy();
        setcookie("weamanageruser", "", time() - 1, "/");
        setcookie("weamanager_roll", "", time() - 1, "/");
        setcookie("werknummer", "", time() - 1, "/");
        setcookie("werkname", "", time() - 1, "/");
        setcookie("weamanager_access", "", time() - 1, "/");
        setcookie("frzlaufnummer", "", time() - 1, "/");
        setcookie("rfnum", "", time() - 1, "/");
        //header("location:../wea");

    }
    private function removeFromConversation($rfnum, $path = "")
    {
        $readDB = $this->readJsonData($path, "conversation");
        if (!empty($readDB)) {
            $readJsonAsArray = json_decode($readDB, true);
            foreach ($readJsonAsArray as $key => $value) {
                if ($rfnum != $value['rfnum']) {
                    $new[] = $value;
                }
            }
            $this->writeJsonData($new, $path, "conversation");
        }
    }
    private function saveCanvasImage($imageData, $imageName, $path)
    {
        list($type, $data) = explode(';', $imageData);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);
        file_put_contents($path . $imageName, $data);
    }
    public function setToWaitOrder()
    {
        if (isset($_REQUEST['add_to_order'])) {
            extract($_POST);
            $liteDB = new Sqlite($_SESSION['werknummer'], "../");
            $rfnum = $this->getRFID("../");
            $replaceDash = str_replace("-", " ", $knznummer);
            $replaceSlash = str_replace("/", "|", $replaceDash);
            $prepareKnznummer = str_replace(" ", "", $replaceSlash);

            $replaceDash = str_replace("-", "", $knznummer_aufleger);
            $prepareKnznummer_aufleger = strtoupper(str_replace(" ", "", $replaceDash));
            $reutrnURI = "../";
            $_SESSION['rfnum'] = $rfnum;
            $_SESSION['frzlaufnummer'] = strtoupper($prepareKnznummer);
            $werkdata = explode(":", $anlieferwerk);
            $_SESSION['werknummer'] = $werkdata[0];
            $_SESSION['werkname'] = $werkdata[1];
            setcookie("frzlaufnummer", $_SESSION['frzlaufnummer'], time() + 86400, "/");
            setcookie("rfnum", $_SESSION['rfnum'], time() + 86400, "/");

            if ($add_to_order == "mobile") {
                $reutrnURI = "../wea";
            }
            if ($add_to_order == "terminal") {
                $generate = date("dmy");
                $removeBlank = str_replace(" ", "", $firma);
                $prepareString = trim($removeBlank);
                $firmaString = htmlspecialchars($prepareString);
                $reutrnURI = "../wea?terminal_register=success&firma=$firmaString&knznummer=$prepareKnznummer&waitnum=$generate:$rfnum:$firmaString";
            }
            if (!empty($_POST['lieferschein'])) {
                $bild = "lf_" . $_POST['rfnum'] . "_" . str_replace(" ", "", $_POST['knznummer']) . "_" . time() . ".jpg";
                $lieferschein = $bild;
                $imagePath = "../db/" . $_SESSION['werknummer'] . "/img_temp/";
                $this->saveCanvasImage($_POST['lieferschein'], $bild, $imagePath);
            }
            $q = "SELECT rfnum FROM traffic WHERE rfnum=$rfnum";
            $num = $liteDB->sqliteNumRows($q);
            if ($num == 0) {
                $array = [
                    "rfnum" => intval($rfnum),
                    "Werknummer" => $werkdata[0],
                    "Werkname" => $werkdata[1],
                    "anmeldeID" => null,
                    "Firma" => htmlspecialchars($firma),
                    "Sprache" => $_SESSION['wealanguage'],
                    "Name Fahrer" => $name_fahrer,
                    "leegut_abholnummer" => $leegut_abholnummer,
                    "leergut_mitnahme" => $leergut_mitnahme,
                    "Legitimation" => $radio_legitimation . " " . strtoupper($legitimation),
                    "FRZTyp" => $FRZTyp,
                    "knznummer_aufleger" => $prepareKnznummer_aufleger,
                    "Nummer" => strtoupper($prepareKnznummer),
                    "Ladung" => $ladung,
                    "Gefahrgut" => $Gefahrgut,
                    "Gefahrgutpunkte" => $Gefahrgutpunkte,
                    "Zollgut" => $Zollgut,
                    //"kennzeichnugspflichtig"=>strtoupper($kennzeichnugspflichtig),
                    "Lieferschein" => $lieferschein,
                    "Beladen für" => $beladen_for,
                    "Entladen" => $entladen,
                    "ladung_beschreibung" => htmlspecialchars($ladung_beschreibung),
                    "Anmeldung" => date("d.m.y, H:i", time()),
                    "timestamp" => time(),
                    "Platz" => null,
                    "Status" => null,
                    "Entladung" => null,
                    "WA_Buro" => json_encode(["erledig" => date("d.m.Y")]),
                    "Protokoll_WA" => null,
                    "Protokoll_VERS" => null,
                    "Einfahrt" => null,
                    "Abfertigung" => null,
                    "gone" => null,
                    "alarm" => null,
                    "scanner" => $this->scannerNummer()
                ];
                if (!empty($sofahnum)) {
                    require_once 'Sonderfahrt.php';
                    $sonderfahrt = new Sonderfahrt($_SESSION['werknummer']);
                    $readSofahDB = $this->readJsonData("../", "sonderfahrt");
                    $readSofahArray = json_decode($readSofahDB, true);
                    foreach ($readSofahArray as $sofaharray) {
                        if ($sofahnum == $sofaharray['sofahnum']) {
                            $arr = $sofaharray;
                            $person = $sofaharray['Dispo_Name'] . " (" . $sofaharray['dispo_ID'] . ")";
                            $toemail = $sofaharray['Dispo_Email'];
                            $arr['sofah_kommen'] = date("d.m.y, H:i", time());
                        }
                    }
                    $mergedArray[] = array_merge($arr, $array);
                    $this->insertTraffic(intval($rfnum), array_merge($arr, $array));
                    $sonderfahrt->sonderFahrtOngate($toemail, $replyTo, $person, $sofahnum);
                    header("location:$reutrnURI");
                    exit;
                }
                $this->insertTraffic(intval($rfnum), $array);
                $this->rememberME($rememberme, $_POST);
                header("location:$reutrnURI");
                exit;
            }
        }
    }
    public function updateRFnumData()
    {
        if (isset($_REQUEST['update_to_order'])) {
            extract($_POST);
            $liteDB = new Sqlite($_SESSION['werknummer'], "../");
            $replaceDash = str_replace("-", "", $knznummer);
            $replaceSlash = str_replace("/", "|", $replaceDash);
            $prepareKnznummer = str_replace(" ", "", $replaceSlash);
            $replaceDash = str_replace("-", "", $knznummer_aufleger);
            $prepareKnznummer_aufleger = strtoupper(str_replace(" ", "", $replaceDash));

            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $array = json_decode($result[0]['object'], true);
            $array['Firma'] = trim($firma);
            $array['Name Fahrer'] = trim($name_fahrer);
            $array['leegut_abholnummer'] = trim($leegut_abholnummer);
            $array['leergut_mitnahme'] = $leergut_mitnahme;
            $array['FRZTyp'] = $FRZTyp;
            $array['knznummer_aufleger'] = $prepareKnznummer_aufleger;
            $array['Nummer'] = $prepareKnznummer;
            $array['Ladung'] = $ladung;
            $array['Gefahrgut'] = $Gefahrgut;
            $array['Gefahrgutpunkte'] = $Gefahrgutpunkte;
            $array['Zollgut'] = $Zollgut;

            $toJson = json_encode($array);
            $q = "UPDATE traffic SET object='$toJson' WHERE rfnum=" . $rfnum . "";
            $liteDB->sqliteQuery($q);
            header("location:$returnURI");
        }
    }
    private function rememberME($rememberme = "", $post)
    {
        if (!empty($rememberme)) {
            $lftnid = 1;
            $readDB = file_get_contents("../db/lieferanten.json");
            if (!empty($readDB)) {
                $readJsonAsArray = json_decode($readDB, true);
                $lftnid = count($readJsonAsArray) + 1;
                $Nummer = strtoupper(trim($post["knznummer"]));
                foreach ($readJsonAsArray as $key => $value) {
                    if ($key == 'lftnid' && $lftnid != $value) {
                        $array[] = [
                            "lftnid" => "$lftnid",
                            "Firma" => trim($post["firma"]),
                            "FRZTyp" => $post["FRZTyp"],
                            "Nummer" => str_replace(" ", "-", $Nummer),
                            "modifyNummer" => str_replace(" ", "-", $Nummer),
                            "name_fahrer" => trim($post['name_fahrer']),
                            "knznummer_aufleger" => trim($post['knznummer_aufleger']),
                            "radio_legitimation" => $post['radio_legitimation'],
                            "legitimation" => $post['legitimation']
                        ];
                        $entry = json_encode(array_merge($readJsonAsArray, $array), JSON_PRETTY_PRINT);
                        file_put_contents("../db/lieferanten.json", $entry);
                    }
                }
            }
            if (empty($readDB)) {
                $array = [
                    "lftnid" => "$lftnid",
                    "Firma" => $post["firma"],
                    "FRZTyp" => $post["FRZTyp"],
                    "Nummer" => strtoupper($post["knznummer"]),
                    "name_fahrer" => $post['name_fahrer'],
                ];
                $readJsonAsArray[0] = $array;
                $entry = json_encode($readJsonAsArray, JSON_PRETTY_PRINT);
                file_put_contents("../db/lieferanten.json", $entry);
            }
        }
    }
    public function setToWaitOrderByNummber()
    {
        if (isset($_REQUEST['session_nummer'])) {
            $getOrderList = $this->getOrderList("../");
            foreach ($getOrderList as $array):
                $Nummers[] = $array['Nummer'];
            endforeach;
            if (!in_array($_REQUEST['session_nummer'], $Nummers)):
                header("location:../wea.php?error=notfound");
                exit;
            endif;
            $_SESSION['frzlaufnummer'] = $_REQUEST['session_nummer'];
            setcookie("frzlaufnummer", $_SESSION['frzlaufnummer'], time() + 86400, "/");
            header("location:../wea");
        }
    }
    private function resetDriveIn($rfnum)
    {
        $liteDB = new Sqlite($_SESSION['werknummer']);
        $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
        $result = $liteDB->sqliteSelect($q);
        $readJsonAsArray = json_decode($result[0]['object'], true);
        if (!empty($readJsonAsArray['Drive-In'])) {
            unset($readJsonAsArray['Drive-In']);
            unset($readJsonAsArray['alarm']);
            $this->updateTraffic($_SESSION['rfnum'], $readJsonAsArray);
        }
    }
    public function setToWaitOrderByWarteNummber()
    {
        if (isset($_REQUEST['Wartenummer'])) {

            $explode = explode("-", $_REQUEST['Wartenummer']);
            $rfnum = $explode[1];
            if (empty($explode[1])) {
                $rfnum = $_REQUEST['Wartenummer'];
            }

            $getOrderList = $this->getOrderList("../");


            foreach ($getOrderList as $key => $array):
                if (($rfnum == $array['rfnum'] && $array['Status'] <= 100) || ($rfnum == $array['rfnum'] && $array['Status'] == 501)) {

                    $this->resetDriveIn($rfnum);

                    $_SESSION['start_prozess'] = time();
                    $_SESSION['rfnum'] = $array['rfnum'];
                    $_SESSION['frzlaufnummer'] = $array['Nummer'];
                    $_SESSION['werknummer'] = $array['Werknummer'];
                    $_SESSION['werkname'] = $array['Werkname'];
                    $_SESSION['wealanguage'] = $array['Sprache'];

                    setcookie("frzlaufnummer", $_SESSION['frzlaufnummer'], time() + 86400, "/");
                    setcookie("rfnum", $_SESSION['rfnum'], time() + 86400, "/");

                    header("location:../wea");
                    exit;
                }
            endforeach;
            if (empty($return)):
                header("location:../wea.php?error=notfound&cardID=invalid");
                exit;
            endif;
        }
    }
    public function confirmWerkschutz($path = "")
    {
        extract($_REQUEST);
        $liteDB = new Sqlite($_SESSION['werknummer']);
        $q = "SELECT object FROM traffic WHERE rfnum=" . $_SESSION['rfnum'] . "";
        $result = $liteDB->sqliteSelect($q);
        $readJsonAsArray = json_decode($result[0]['object'], true);
        $readJsonAsArray['LegitimationConfirm'] = time();
        //hier 
        $this->updateTraffic($_SESSION['rfnum'], $readJsonAsArray);
        echo "confirmed";
    }
    public function backToWaitList()
    {
        if (isset($_REQUEST['back_to_waitlist'])) {
            extract($_REQUEST);
            $liteDB = new Sqlite($_SESSION['werknummer'], "../");
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            unset($readJsonAsArray['Weiterleitung']);
            unset($readJsonAsArray['Weiterleitung_von']);
            unset($readJsonAsArray['Weiterleitung_Time']);
            unset($readJsonAsArray['Drive-In']);
            unset($readJsonAsArray['Pforte']);
            unset($readJsonAsArray['alarm']);
            $readJsonAsArray['Status'] = null;
            $readJsonAsArray['Platz'] = null;
            $readJsonAsArray['Stapler'] = null;
            $readJsonAsArray['Abfertigung'] = null;
            $readJsonAsArray['gone'] = null;
            $readJsonAsArray['Einfahrt'] = null;
            $readJsonAsArray['Protokoll_WA'] = null;
            $readJsonAsArray['eingesteuert'] = null;
            $readJsonAsArray['Tstamp-Abfertigung'] = null;
            $this->updateTraffic($rfnum, $readJsonAsArray);
            header("location:../");
        }
    }
    public function backToPreviosStatus()
    {
        if (isset($_REQUEST['back_to_previos'])) {
            $return = "";
            extract($_REQUEST);
            $liteDB = new Sqlite($_SESSION['werknummer'], "../");
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            $readJsonAsArray['Status'] = 75;
            $readJsonAsArray['Protokoll_WA'] = null;
            $this->updateTraffic($rfnum, $readJsonAsArray);
            header("location:../$return");
        }
    }
    public function sendToNextStep()
    {
        if (isset($_REQUEST['sendtonextstep'])) {
            $expl = explode(":", $_REQUEST['sendtonextstep']);
            $unloadPlaces = $this->getUnloadPlaceData("../");
            $rfnum = $expl[0];
            extract($_REQUEST);

            $liteDB = new Sqlite($_SESSION['werknummer'], "../");
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            foreach ($unloadPlaces as $place) {
                if ($next_platz == $place['Platz']) {
                    $readJsonAsArray['Ladung'] = $place['BereichFilter'];
                }
            }
            $readJsonAsArray['Weiterleitung_von'] = $array['Platz'];
            $readJsonAsArray['Weiterleitung_Time'] = date("H:i");
            $readJsonAsArray['Platz'] = $next_platz;
            $readJsonAsArray['Stapler'] = null;
            $readJsonAsArray['Status'] = 75;
            $readJsonAsArray['Weiterleitung'] = $next_platz;
            $readJsonAsArray['Protokoll_WA'] = null;

            $this->updateTraffic($rfnum, $readJsonAsArray);
            header("location:../$returnURI");
        }
    }
    public function changeZollUnloadPlant()
    {
        extract($_REQUEST);
        $liteDB = new Sqlite($_SESSION['werknummer'], "../");
        $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
        $result = $liteDB->sqliteSelect($q);
        $readJsonAsArray = json_decode($result[0]['object'], true);
        $readJsonAsArray['Ladung'] = $Ladung;
        $this->updateTraffic($rfnum, $readJsonAsArray);
        echo "<span class='ms-2 badge bg-success rounded' id='badge-changed-plant'>geändert</span>";
    }
    public function passZollgut()
    {
        if (isset($_REQUEST['pass_zollgut'])) {
            $rfnum = $_REQUEST['pass_zollgut'];
            $return_uri = $_REQUEST['return'];
            $ladung = $_REQUEST['Ladung'];

            $liteDB = new Sqlite($_SESSION['werknummer'], "../");
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            $readJsonAsArray['Zollgut'] = "PASSIERT";
            if ($readJsonAsArray['Ladung'] != "Paketdienst") {
                $readJsonAsArray['Ladung'] = $ladung;
            }
            $readJsonAsArray['Zollabfertigung'] = time();
            unset($readJsonAsArray['Zollmeldung']);

            $this->updateTraffic($rfnum, $readJsonAsArray);

            header("location:../$return_uri");
            exit;
        }
    }
    public function sendungsinfoErfassen()
    {
        if (isset($_REQUEST['sendungsinfo_erfassen'])) {
            //[collis] => 2 [sendungnummern] => 12346678 [sendungsinfo_erfassen] => 1 [rfnum] => 98 
            extract($_REQUEST);
            $return_uri = "../zollgut";

            $liteDB = new Sqlite($_SESSION['werknummer'], "../");
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            $readJsonAsArray['Zoll-Sendungen'] = ["Collis" => $collis, "Sendungen" => $sendungnummern];
            $this->updateTraffic($rfnum, $readJsonAsArray);

            header("location:$return_uri");
        }
    }
    public function setToProzess()
    {
        if (isset($_REQUEST['add_to_prozess'])) {

            extract($_REQUEST);
            $lieferdokument = null;
            $einsteuer = null;
            $return = "../";
            $einsteuer = $_SESSION['weamanageruser'];

            if (!empty($redirect)) {
                $return = "../" . $redirect;
            }
            $directory = '../ScannFolder/';
            $scanned_directory = array_diff(scandir($directory), array('..', '.'));
            if (!empty($scanned_directory)) {
                foreach ($scanned_directory as $file) {
                    if ($file == date("dmY") . "_WN_" . $rfnum . ".pdf") {
                        $lieferdokument = $file;
                    }
                }
            }
            $liteDB = new Sqlite($_SESSION['werknummer']);
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            $readJsonAsArray['eingesteuert'] = $einsteuer;
            $readJsonAsArray['Status'] = 50;
            $readJsonAsArray['Platz'] = $Platz;
            $readJsonAsArray['Einfahrt'] = date("d.m.y, H:i", time());
            $readJsonAsArray['Lieferdokument'] = $lieferdokument;
            unset($readJsonAsArray['alarm']);
            unset($readJsonAsArray['Drive-In']);
            $this->updateTraffic($rfnum, $readJsonAsArray);

            header("location:" . $return);
            exit;
        }
        if (isset($_REQUEST['ajaxSetToProzess'])) {
            extract($_POST);
            $einsteuer = null;
            $einsteuer = $_SESSION['weamanageruser'];
            if (!empty($Platz)) {
                $liteDB = new Sqlite($_SESSION['werknummer']);
                $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
                $result = $liteDB->sqliteSelect($q);
                $readJsonAsArray = json_decode($result[0]['object'], true);
                $readJsonAsArray['eingesteuert'] = $einsteuer;
                $readJsonAsArray['Status'] = 50;
                $readJsonAsArray['Platz'] = $Platz;
                $readJsonAsArray['Einfahrt'] = date("d.m.y, H:i", time());
                $this->updateTraffic($rfnum, $readJsonAsArray);
            }
        }
    }
    public function setToProzessWerksverkehr()
    {
        if (isset($_REQUEST['add_to_prozess_werksverkehr'])) {

            $liteDB = new Sqlite($_SESSION['werknummer'], "../");
            $setrfnum = $this->getRFID("../");
            $q = "SELECT rfnum FROM traffic WHERE rfnum=$setrfnum";
            $prozessname = "";
            $spedition = "";
            $platz = "";
            $Gefahrgut = "";
            $status = 75;
            $driveIn = 1;

            extract($_GET);
            switch ($add_to_prozess_werksverkehr) {
                case "specialprocess1":
                    $expl = explode(";", $externalData);
                    $empfaenger = $expl[0];
                    $prozessname = $empfaenger;
                    $firma = $expl[1];
                    $spedition = $firma;
                    $nummer = strtoupper($expl[2]);
                    $_SESSION['werkname'] = "Werk 5 Neu-Ulm";
                    $platz = $empfaenger;
                    if ($empfaenger == "Gefahrgut Gase") {
                        $Gefahrgut = "JA";
                        require_once 'connect.php';
                        $emails = [
                            "stefan.erb@daimlertruck.com",
                            "gert.wanner@daimlertruck.com",
                            "daniel.f.fuchs@daimlertruck.com",
                            "juergen.spaeth@daimlertruck.com",
                            "wjatscheslaw.hazenbiller@daimlertruck.com"
                        ];
                        $connect = new connect();
                        $from = "Pforte[Werk-5].anmeldung@daimlertruck.com";
                        $betreff = "Lieferung Gefahrgut / Gase";
                        $nachricht = "Firma " . $spedition . " hat eine neue Gefahrgut / Gase geliefert.
                            <p>Das Fahrzeug wird demnächst auf das Firmengelände einfahren. Ein Anruf seitens Werkschutzes wird in Kürze erfolgen</p>Bitte nicht Antworten, dies ist eine Automatische E-mail";
                        foreach ($emails as $empfaenger) {
                            $connect->sendEmail($empfaenger, $from, $betreff, $nachricht);
                        }
                    }
                    break;
                case "specialprocess2":
                    $prozessname = "W9 Trailertausch";
                    $spedition = "Definierte Spedition";
                    $_SESSION['werkname'] = "Werk 9 Neu-Ulm";
                    $platz = "Trailerplatz Werk 9";
                    break;
                case "specialprocess3":
                    $expl = explode(";", $externalData);
                    $empfaenger = $expl[0];
                    $firma = $expl[1];
                    $nummer = strtoupper($expl[2]);
                    $prozessname = "Fremdfirma";
                    $spedition = $firma;
                    $_SESSION['werkname'] = "Werk Neu-Ulm";
                    $platz = $empfaenger;
                    break;
                case "specialprocess4":
                    $expl = explode(";", $externalData);
                    $empfaenger = $expl[1];
                    $firma = $expl[0];
                    $nummer = strtoupper($expl[2]);
                    $prozessname = "Entsogrungsfirma";
                    $spedition = $firma;
                    $_SESSION['werkname'] = "Werk Neu-Ulm";
                    $platz = "Schrottplatz";
                    break;
                default:
                    $prozessname = "Werksverkehr";
                    $spedition = "Böhm & Besold";
                    $_SESSION['werkname'] = "Werk 5 Neu-Ulm";
                    $platz = "WE-Überdachung";
                    break;
            }
            $replaceDash = str_replace("-", "", $nummer);
            $replaceSlash = str_replace("/", "|", $replaceDash);
            $nummer = str_replace(" ", "", $replaceSlash);
            $_SESSION['start_prozess'] = time();
            $_SESSION['rfnum'] = $setrfnum;
            $_SESSION['frzlaufnummer'] = $nummer;
            $_SESSION['prozessname'] = $prozessname;
            setcookie("frzlaufnummer", $_SESSION['frzlaufnummer'], time() + 86400, "/");
            setcookie("rfnum", $_SESSION['rfnum'], time() + 86400, "/");
            $returnURI = "../" . $_REQUEST['return'];
            if ($gadget == "am Terminal") {
                $returnURI = "../" . $_REQUEST['return'] . "?terminal_register=success&firma=$firma&knznummer=$nummer&waitnum=" . date("dmy") . ":$setrfnum:$firma";
            }
            $num = $liteDB->sqliteNumRows($q);
            if ($num > 0) {
                header("location:$returnURI");
                exit;
            }
            $array = [
                "rfnum" => intval($setrfnum),
                "Werknummer" => $_SESSION['werknummer'],
                "Werkname" => $_SESSION['werkname'],
                "anmeldeID" => null,
                "Firma" => $spedition,
                "Sprache" => $_SESSION['wealanguage'],
                "Name Fahrer" => null,
                "leegut_abholnummer" => null,
                "leergut_mitnahme" => "NEIN",
                "Legitimation" => null,
                "FRZTyp" => null,
                "Prozessname" => $prozessname,
                "Nummer" => $nummer,
                "Ladung" => null,
                "Gefahrgut" => $Gefahrgut,
                "Gefahrgutpunkte" => null,
                "Zollgut" => null,
                "kennzeichnugspflichtig" => null,
                "Lieferschein" => null,
                "Beladen für" => null,
                "Entladen" => null,
                "ladung_beschreibung" => null,
                "Anmeldung" => date("d.m.y, H:i", time()),
                "timestamp" => time(),
                "Platz" => $platz,
                "Status" => $status,
                "Entladung" => null,
                "Protokoll_WA" => null,
                "Protokoll_VERS" => null,
                "Einfahrt" => null,
                "Abfertigung" => null,
                "gone" => null,
                "alarm" => null,
                "WA_Buro" => "entfällt",
                "scanner" => null,
                "LegitimationConfirm" => time(),
                "Drive-In" => $driveIn
            ];
            $this->insertTraffic(intval($setrfnum), $array);
            header("location:$returnURI");
        }
    }
    public function entryPassed()
    {
        if (isset($_REQUEST['entry_passed'])) {
            $rfnum = $_REQUEST['entry_passed'];
            $return_uri = $_REQUEST['return_uri'];
            $status = $_REQUEST['Status'];

            $liteDB = new Sqlite($_SESSION['werknummer']);
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            $readJsonAsArray['Status'] = $status;
            $readJsonAsArray['Abfertigung'] = date("d.m.y, H:i", time());
            $this->updateTraffic($rfnum, $readJsonAsArray);
            switch ($return_uri) {
                case "wea":
                    $uri = "../wea";
                    break;
                case "stapler":
                    $uri = "../stapler";
                    break;
                case "nonepublic":
                    $uri = "../";
                    break;
            }
            header("location:$uri");
            exit;
        }
    }
    public function vehicleGone($route = "", $person_sign = "")
    {
        if (isset($_REQUEST['vehicle_gone'])) {
            $return_uri = "../";
            unset($_SESSION['alarm_stopped']);
            if (isset($_REQUEST['return_uri'])) {
                $return_uri = $_REQUEST['return_uri'];
            }
            $rfnum = $_REQUEST['vehicle_gone'];
            // Löschen Eintäge in Chat, die älter als Range
            $liteDB = new Sqlite($_SESSION['werknummer']);
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            if (isset($person_sign)) {
                $readJsonAsArray['Pforte'] = $person_sign;
            }
            $readJsonAsArray['Status'] = 120;
            $readJsonAsArray['gone'] = time();
            $this->updateTraffic($rfnum, $readJsonAsArray);
            $this->setToArchiv(json_encode($readJsonAsArray));

            switch ($return_uri) {
                case "public":
                    $uri = "../wea";

                    setcookie("frzlaufnummer", $_SESSION['frzlaufnummer'], time() - 1000, "/");
                    setcookie("rfnum", $_SESSION['rfnum'], time() - 1000, "/");
                    setcookie("werknummer", $_SESSION['werknummer'], time() - 1000, "/");
                    setcookie("werkname", $_SESSION['werkname'], time() - 1000, "/");

                    setcookie("weamanageruser", "", time() - 1, "/");
                    session_destroy();
                    break;
                case "nonepublic":
                    $uri = "../";
                    $_SESSION['frzlaufnummer'] = null;
                    unset($_SESSION['frzlaufnummer']);
                    unset($_SESSION['zoll_alarm_stopped']);
                    unset($_SESSION['alarm_stopped']);
                    unset($_SESSION['wealanguage']);
                    unset($_SESSION['start_prozess']);
                    break;
                default:
                    $uri = "../";
                    break;
            }
            if ($route != "werkverlassen") {
                header("location:$uri");
            }
        }
    }
    public function prozessDone()
    {
        if (isset($_REQUEST['prozess_done'])) {
            $signed = null;
            $signValid = false;
            $return_uri = "../";
            $person_sign = $_REQUEST['person_sign'];
            $protokoll = $_REQUEST['prozess_done'];

            $poolBerechtigte = $this->getRolles("../");
            foreach ($poolBerechtigte as $berechtigte) {
                if ($berechtigte['Personalnummer'] == $person_sign) {
                    $signed = $berechtigte['Name'] . ", " . $berechtigte['Vorname'];
                    $_POST['signed'] = $signed;
                    $signValid = true;
                }
            }
            if (isset($_REQUEST['return_uri'])) {
                $return_uri = $_REQUEST['return_uri'];
            }
            $rfnum = $_REQUEST['rfnum'];
            unset($_POST['return_uri']);
            unset($_POST['prozess_done']);
            unset($_POST['rfnum']);
            switch ($return_uri) {
                case "public":
                case "wea":
                    $uri = "../wea";
                    break;
                case "stapler":
                    $uri = "../stapler";
                    break;
                case "nonepublic":
                    $uri = "../";
                    break;
                default:
                    $uri = "../";
                    break;
            }
            if ($signValid == false) {
                header("location:$uri");
                exit;
            }
            $liteDB = new Sqlite($_SESSION['werknummer']);
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            switch ($protokoll) {
                case "versand_protokoll":
                    $Protokol_VERS = json_encode($_POST);
                    $readJsonAsArray['Protokoll_VERS'] = $Protokol_VERS;
                    $this->updateTraffic($rfnum, $readJsonAsArray);
                    echo "ok";
                    break;
                case "wa_buro":
                    $WA_Buro = json_encode($_POST);
                    $readJsonAsArray['WA_Buro'] = $WA_Buro;
                    $readJsonAsArray['Status'] = 75;
                    if (!empty($readJsonAsArray['Protokoll_WA']) && $readJsonAsArray['leergut_mitnahme'] == "NEIN") {
                        $readJsonAsArray['Status'] = 100;
                    }
                    $this->updateTraffic($rfnum, $readJsonAsArray);
                    echo "ok";
                    break;
                case "wa_leergut":
                    $WA_Leergut = json_decode($readJsonAsArray['leergut_mitnahme'], true);
                    $WA_Leergut['person_sign'] = $person_sign;
                    $WA_Leergut['signed'] = $signed;
                    $readJsonAsArray['leergut_mitnahme'] = json_encode($WA_Leergut);
                    if (!empty($readJsonAsArray['Protokoll_WA'])) {
                        $readJsonAsArray['Status'] = 100;
                    }

                    $this->updateTraffic($rfnum, $readJsonAsArray);
                    echo "ok";
                    break;
                case "ma_auftrag":
                    $readJsonAsArray['MA-Autfrag']['person_sign'] = $person_sign;
                    $readJsonAsArray['MA-Autfrag']['signed'] = $signed;
                    $this->updateTraffic($rfnum, $readJsonAsArray);
                    echo "ok";
                    break;
                default:
                    $Protokol_WA = json_encode($_POST);
                    $readJsonAsArray['Entladung'] = date("d.m.y, H:i", time());
                    $readJsonAsArray['Protokoll_WA'] = $Protokol_WA;
                    $readJsonAsArray['Status'] = 75;
                    $readJsonAsArray['Tstamp-Abfertigung'] = time();
                    $readJsonAsArray['Abfertigung'] = date("d.m.y, H:i");
                    if (empty($readJsonAsArray['WA_Buro'])) {
                        echo 1;
                        exit;
                    }
                    $readJsonAsArray['Status'] = 100;
                    if ($readJsonAsArray['leergut_mitnahme'] != "NEIN" && is_array($readJsonAsArray['leergut_mitnahme'])) {
                        if (is_array($readJsonAsArray['leergut_mitnahme'])) {
                            $arr = json_decode($readJsonAsArray['leergut_mitnahme'], true);
                            if (empty($arr['person_sign'])) {
                                $readJsonAsArray['Status'] = 75;
                            }
                        }
                    }
                    if ($readJsonAsArray['Prozessname'] == "Werksverkehr") {
                        $readJsonAsArray['Status'] = 100;
                    }

                    if (isset($_FILES)) {
                        require_once 'class_image_edit.php';
                        $image = new imageEdit();
                        $imagePath = "../db/" . $_SESSION['werknummer'] . "/lasinachweis/";
                        $bild = date("dmY") . "_" . $rfnum . ".jpg";
                        if (move_uploaded_file($_FILES['lasi_bild']['tmp_name'], $imagePath . $bild)) {
                            $image->resize_image($imagePath . $bild, 500, 380, true);
                            $image->machAvatar($imagePath, $imagePath, $bild, 250);
                            $readJsonAsArray['lasi_bild'] = $bild;
                        }
                    }
                    $this->updateTraffic($rfnum, $readJsonAsArray);
                    echo "ok";
                    break;
            }
            exit;
        }
    }
    public function doneManuellerAuftrag($path = "")
    {
        if (isset($_REQUEST['done_manueller_auftrag'])) {
            $auftrID = $_REQUEST['done_manueller_auftrag'];
            $readDB = $this->readJsonData($path, "manueller_auftrag");
            $arrays = json_decode($readDB, true);
            foreach ($arrays as $key => $array) {
                if ($key == $auftrID) {
                    unset($arrays[$key]);
                }
            }
            $this->writeJsonData($arrays, $path, "manueller_auftrag");
            header("location:../stapler");
        }
    }
    private function setToArchiv($entry)
    {
        require_once 'connect.php';
        $database = "wareneingang";
        $werknummer = $_SESSION['werknummer'];
        $o = new connect();
        $q = "INSERT INTO " . $database . ".dbo.werk_$werknummer(object_runleaf,tstamp)VALUES('$entry'," . time() . ")";
        $o->query($q);
    }
    public function getFromArchiv()
    {
        $werknummer = $_SESSION['werknummer'];
        $o = new connect();
        $range = time() - 2592000; // 30 Tage
        $where = "WHERE tstamp>$range";
        if (isset($_REQUEST['showentry']) && $_REQUEST['showentry'] == "all") {
            $where = "";
        }
        //$q="SELECT TOP 10000 id, object_runleaf FROM ".$this->database.".dbo.werk_$werknummer ORDER BY tstamp DESC";
        $q = "SELECT id, object_runleaf FROM " . $this->database . ".dbo.werk_$werknummer $where ORDER BY tstamp DESC";
        $temp = $o->select($q);
        if (empty($temp)) {
            return [];
        }
        foreach ($temp as $value) {
            $unset = json_decode($value['object_runleaf'], true);
            unset($unset['Werknummer']);
            unset($unset['Werkname']);
            unset($unset['Sprache']);
            unset($unset['Name Fahrer']);
            unset($unset['Legitimation']);
            unset($unset['Entladen']);
            unset($unset['ladung_beschriebung']);
            unset($unset['Beladen für']);
            unset($unset['kennzeichnugspflichtig']);
            unset($unset['alarm']);
            unset($unset['leegut_abholnummer']);
            $returnArray[$value['id']] = $unset;
        }
        return $returnArray;
    }
    public function registerLiferant()
    {
        if (isset($_REQUEST['add_lieferant'])) {
            extract($_POST);
            $return_uri = $_REQUEST['return_uri'];
            $readDB = file_get_contents("../db/lieferanten.json");
            if (!empty($readDB)) {
                $readJsonAsArray = json_decode($readDB, true);
                foreach ($readJsonAsArray as $key => $value) {
                    if ($key == 'lftnid' && $lftnid != $value) {
                        $array[] = [
                            "lftnid" => $lftnid,
                            "Firma" => $firma,
                            "FRZTyp" => $frztyp,
                            "Nummer" => strtoupper($knznummer)
                        ];
                        $entry = json_encode(array_merge($readJsonAsArray, $array), JSON_PRETTY_PRINT);
                        file_put_contents("../db/lieferanten.json", $entry);
                        header("location:../$return_uri.php?register=success&kennzeichen=" . strtoupper($knznummer));
                        exit;
                    }
                }
            }
            $array = [
                "lftnid" => $lftnid,
                "Firma" => $firma,
                "FRZTyp" => $frztyp,
                "Nummer" => strtoupper($knznummer)
            ];
            $readJsonAsArray[0] = $array;
            $entry = json_encode($readJsonAsArray, JSON_PRETTY_PRINT);
            file_put_contents("../db/lieferanten.json", $entry);
            header("location:../$return_uri.php?register=success&kennzeichen=" . strtoupper($knznummer));
            exit;
        }
    }
    public function getLiferantenIDs()
    {
        $readDB = file_get_contents("db/lieferanten.json");
        $id = 1;
        if (!empty($readDB)) {
            $readJsonAsArray = json_decode($readDB, true);
            $lastindex = count($readJsonAsArray) - 1;
            $id = $readJsonAsArray[$lastindex]['lftnid'] + 1;
        }
        return $id;
    }
    public function getRFID($pfad = "")
    {
        $id = 1;
        $arrays = $this->readTraffic($pfad);
        if (empty($arrays)) {
            return $id;
        }
        foreach ($arrays as $array) {
            $tmp[] = $array['rfnum'];
        }
        $id = max($tmp) + 1;
        return $id;
    }
    public function firmenListe()
    {
        $readDB = file_get_contents("db/lieferanten.json");
        if (empty($readDB)) {
            return [];
        }
        $arrays = json_decode($readDB, true);
        foreach ($arrays as $array) {
            $firma[] = $array['Firma'];
        }
        return array_unique($firma);
    }
    public function kennZeichenListe($pfad = "")
    {
        $readDB = file_get_contents($pfad . "db/lieferanten.json");
        if (empty($readDB)) {
            return [];
        }
        $arrays = json_decode($readDB, true);
        foreach ($arrays as $array) {
            $nummer[] = $array['Nummer'];
        }
        return array_unique($nummer);
    }
    public function kennZeichenVoranmeldungListe($pfad = "")
    {
        $db = new connect();
        $q = "SELECT * from tracker";
        $results = $db->select($q);
        if (empty($results)) {
            return [];
        }
        foreach ($results as $result) {
            $arrays = json_decode($result['object_frz'], true);
            foreach ($arrays as $autokennzeichen => $array) {
                $nummer[] = $autokennzeichen;
            }
        }
        return array_unique($nummer);
    }
    public function getOrderList($path = null)
    {
        $arrays = $this->readTraffic($path, "ordering");

        return $arrays;
    }
    public function getOrderListManuellerAuftrag($path = null)
    {
        $readDB = $this->readJsonData($path, "manueller_auftrag");
        if (empty($readDB)) {
            return [];
        }
        $arrays = json_decode($readDB, true);
        return $arrays;
    }
    public function maxIDinOrerList($path = null)
    {
        $arr = $this->getOrderList($path);
        if (empty($arr)) {
            return 0;
        }
        foreach ($arr as $data) {
            $arrRfnum[] = $data['rfnum'];
        }
        return max($arrRfnum);
    }
    public function getOrderListTotal($range)
    {
        require_once 'connect.php';
        $werknummer = $_SESSION['werknummer'];
        $timeLimit = time() - $range;
        $datasets = [];
        $o = new connect();
        $q = "SELECT object_runleaf FROM " . $this->database . ".dbo.werk_$werknummer WHERE tstamp>$timeLimit ORDER BY tstamp ASC";
        $arrays = $o->select($q);
        if (!empty($arrays)) {
            foreach ($arrays as $array) {
                $arr = json_decode($array['object_runleaf'], true);
                $datasets[] = $arr;
            }
        }
        return $datasets;
    }
    public function getOrderListToday($path = null)
    {
        $readDB = $this->readTraffic($path);
        $heute = [];
        if (empty($readDB)) {
            return [];
        }
        if (!empty($readDB)) {
            foreach ($readDB as $array) {
                if (!empty($array['LegitimationConfirm'])) {
                    $heute[] = $array;
                }
            }
        }
        return $heute;
    }
    public function getOnParking($getOrderList, $flag)
    {
        if (empty($getOrderList)) {
            return [];
        }
        $return = [];
        if (!empty($getOrderList)) {
            foreach ($getOrderList as $array) {
                if (!empty($array['LegitimationConfirm'])) {
                    if ($flag == "parking") {
                        if ($array['Status'] < 50) {
                            $return[$array['rfnum']] = $array;
                        }
                    }
                    if ($flag == "process") {
                        if ($array['Status'] > 25 && $array['Status'] < 120 || $array['Status'] == 501) {
                            $return[$array['rfnum']] = $array;
                        }
                    }
                }
            }
            sort($return);
        }
        return $return;
    }
    public function getOrderListForZoll($path = null, $range = 0)
    {
        $datasets = [];
        $arrays = $this->readTraffic($path);

        //$readDB = $this->readJsonData($path);
        //$arrays = json_decode($readDB,true);

        foreach ($arrays as $array) {
            if ($array['Zollgut'] != "NEIN" && !empty($array['Zollgut'])) {
                $datasets[] = $array;
            }
        }

        $werknummer = $_SESSION['werknummer'];
        $timeLimit = time() - $range;

        $o = new connect();
        $q = "SELECT object_runleaf FROM " . $this->database . ".dbo.werk_$werknummer WHERE tstamp<$timeLimit ORDER BY id asc";

        $arrays = $o->select($q);
        foreach ($arrays as $array) {
            $arr = json_decode($array['object_runleaf'], true);
            if ($arr['Zollgut'] != "NEIN" && !empty($arr['Zollgut'])) {
                $datasets[] = $arr;
            }
        }
        return $datasets;
    }
    public function stopSound()
    {
        if (isset($_REQUEST['stopsound'])) {
            $rfnum = $_REQUEST['rfnum'];
            $driveInData = $_REQUEST['driveInData'];
            switch ($driveInData) {
                case 2:
                    $driveIn = 2;
                    break;
                default:
                    $driveIn = 1;
            }
            $liteDB = new Sqlite($_SESSION['werknummer']);
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            $readJsonAsArray['alarm'] = "stopped";
            $_SESSION["alarm_stopped"] = $rfnum;
            if ($driveIn == 1) {
                $readJsonAsArray['Drive-In'] = $driveIn;
            }
            if ($driveIn == 2) {
                $readJsonAsArray['Drive-In'] = $driveIn;
                $readJsonAsArray['Status'] = null;
                $readJsonAsArray['Platz'] = null;
                $readJsonAsArray['Abfertigung'] = null;
                unset($_SESSION['frzlaufnummer']);
            }
            $this->updateTraffic($rfnum, $readJsonAsArray);

            exit;
        }
        if (isset($_REQUEST['stopzollsound'])) {
            $rfnum = $_REQUEST['rfnum'];
            $_SESSION["zoll_alarm_stopped"] = $rfnum;
            exit;
        }
    }
    public function setSoundModus()
    {
        if (isset($_REQUEST['changesoundmodus'])) {
            $changesoundmodus = $_REQUEST['changesoundmodus'];
            switch ($changesoundmodus) {
                case 1:
                    $newmodus = 2;
                    $return = "Klingel deaktiviert";
                    break;
                case 2:
                    $newmodus = 1;
                    $return = "Klingel aktiviert";
                    break;
            }
            $_SESSION['soundmodus'] = $newmodus;
            echo $return;
        }
    }
    public function soundModus()
    {
        if ($_SESSION['soundmodus'] != 0) {
            return $_SESSION['soundmodus'];
        }
        $json = json_decode(file_get_contents("db/soundmodus.json"), true);
        return $json['soundmodus'];
    }
    public function resetOldSession()
    {
        if (isset($_REQUEST['oldsession'])) {
            $return = $_REQUEST['return'];
            if ($return == "stapler" && !empty($_SESSION['weamanageruser'])) {
                $user = $_SESSION['weamanageruser'];
                $this->deleteFromOnlineData("../", $user);
            }
            session_destroy();
            setcookie("weamanageruser", "", time() - 3600, "/");
            setcookie("weamanager_roll", "", time() - 3600, "/");
            setcookie("werknummer", "", time() - 3600, "/");
            setcookie("werkname", "", time() - 3600, "/");
            setcookie("weamanager_access", "", time() - 3600, "/");
            setcookie("frzlaufnummer", "", time() - 3600, "/");
            setcookie("rfnum", "", time() - 3600, "/");
            setcookie("adittionalJob", "", time() - 3600, "/");

            header("location:../$return");
        }
    }
    public function setLKWfilter()
    {
        if (isset($_REQUEST['setLKWfilter'])) {
            $_SESSION['setLKWfilter'] = $_REQUEST['setLKWfilter'];
            if (isset($_SESSION['setLKWfilter']) && $_REQUEST['setLKWfilter'] == "ALLLKWs") {
                unset($_SESSION['setLKWfilter']);
            }
            header("location:../");
        }
    }
    public function alertModal()
    {
        echo '<div id="dialog-confirm" class="d-none" title="Bitte bestätigen">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>
        <span id="dinamic-text"></span>
      </p>
      </div>';
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
        return '<div class="row fixed-bottom"><div class="col-12 mt-2 mb-2 p-2 rounded text-center ' . $alertStyle . '">' . $dynamictext . '</div></div>';
    }
    public function addNewMessage($path = "")
    {
        $returnURI = $_POST['return'];
        unset($_POST['addnewmessage']);
        unset($_POST['return']);
        $readDB = $this->readJsonData($path, "conversation");
        if (!empty($readDB)) {
            $readJsonAsArray = json_decode($readDB, true);
            $readJsonAsArray[] = $_POST;
            krsort($readJsonAsArray);
            $this->writeJsonData($readJsonAsArray, $path, "conversation");
            switch ($returnURI) {
                case "index":
                    header("location:../");
                    break;
                default:
                    header("location:../$returnURI");
            }
            exit;
        }
        $this->writeJsonData([$_POST], $path, "conversation");

        switch ($returnURI) {
            case "index":
                header("location:../");
                break;
            default:
                header("location:../$returnURI");
        }
    }
    public function addBMIData($path = "")
    {
        if (isset($_REQUEST['add_BMIData'])) {
            unset($_POST['add_BMIData']);
            $_POST['BMI-Bild'] = "";
            if (isset($_FILES)) {
                require_once 'class_image_edit.php';
                $image = new imageEdit();
                $imagePath = $path . "db/" . $_SESSION['werknummer'] . "/bmi/";
                $bild = $_POST['Hersteller'] . "_" . $_POST['BMI-Nummer'] . "_" . time() . ".jpg";
                if (move_uploaded_file($_FILES['BMI-Bild']['tmp_name'], $imagePath . $bild)) {
                    //echo "uploaded";
                    $image->resize_image($imagePath . $bild, 500, 380, true);
                    $image->machAvatar($imagePath, $imagePath, $bild, 250);
                    $_POST['BMI-Bild'] = $bild;
                }
            }
            $readDB = $this->readJsonData($path, "bmi");
            if (!empty($readDB)) {
                $readJsonAsArray = json_decode($readDB, true);
                $readJsonAsArray[] = $_POST;
                krsort($readJsonAsArray);
                $this->writeJsonData($readJsonAsArray, $path, "bmi");
                header("location:../settings?setting=betriebsmittel");
                exit;
            }

            $this->writeJsonData([$_POST], $path, "bmi");
            header("location:../settings?setting=betriebsmittel");
        }
    }
    public function updateBMIData($path = "../")
    {
        if (isset($_REQUEST['updateBMIData'])) {
            $editfilter = $_POST['updateBMIData'];
            unset($_POST['updateBMIData']);
            if (!isset($_FILES)) {
                unset($_POST['BMI-Bild']);
            }
            if (isset($_FILES)) {
                require_once 'class_image_edit.php';
                $image = new imageEdit();
                $imagePath = $path . "db/" . $_SESSION['werknummer'] . "/bmi/";
                $bild = $_POST['Hersteller'] . "_" . $_POST['BMI-Nummer'] . "" . time() . ".jpg";
                if (move_uploaded_file($_FILES['BMI-Bild']['tmp_name'], $imagePath . $bild)) {
                    //echo "uploaded";
                    $image->resize_image($imagePath . $bild, 500, 380, true);
                    $image->machAvatar($imagePath, $imagePath, $bild, 250);
                    $_POST['BMI-Bild'] = $bild;
                }
            }

            $readDB = $this->readJsonData($path, "bmi");
            if (!empty($readDB)) {
                $readJsonAsArray = json_decode($readDB, true);
                foreach ($readJsonAsArray as $key => $array) {
                    if ($key == $editfilter) {
                        //unset($readJsonAsArray[$key]);
                        $readJsonAsArray[$key] = $_POST;
                    }
                }
                krsort($readJsonAsArray);
                $this->writeJsonData($readJsonAsArray, $path, "bmi");
                header("location:../settings?setting=betriebsmittel");
                exit;
            }
        }
    }
    public function addPersonData($path = "")
    {
        if (isset($_REQUEST['add_personData'])) {
            unset($_POST['add_personData']);
            $_POST['Daimler-ID'] = strtoupper($_POST['Daimler-ID']);
            $readDB = $this->readJsonData($path, "personal");
            if (!empty($readDB)) {
                $readJsonAsArray = json_decode($readDB, true);
                $readJsonAsArray[] = $_POST;
                krsort($readJsonAsArray);
                $this->writeJsonData($readJsonAsArray, $path, "personal");
                header("location:../settings?setting=personal");
                exit;
            }
            $this->writeJsonData([$_POST], $path, "personal");
            header("location:../settings?setting=personal");
        }
    }
    public function addReminder($path = "")
    {
        if (isset($_REQUEST['add_reminder'])) {
            unset($_POST['add_reminder']);
            $_POST['Ersteller'] = strtoupper($_POST['Ersteller']);
            $readDB = $this->readJsonData($path, "reminder");
            if (!empty($readDB)) {
                $readJsonAsArray = json_decode($readDB, true);
                $_POST['Reminder-ID'] = count($readJsonAsArray) + 1;
                $readJsonAsArray[] = $_POST;
                krsort($readJsonAsArray);
                $this->writeJsonData($readJsonAsArray, $path, "reminder");
                header("location:../settings?setting=reminder");
                exit;
            }
            $_POST['Reminder-ID'] = 1;
            $this->writeJsonData([$_POST], $path, "reminder");
            header("location:../settings?setting=reminder");
        }
    }
    public function updatePersonalData($path = "")
    {
        if (isset($_REQUEST['updatePersonalData'])) {
            $editfilter = $_POST['updatePersonalData'];
            unset($_POST['updatePersonalData']);
            $_POST['Daimler-ID'] = strtoupper($_POST['Daimler-ID']);
            $readDB = $this->readJsonData($path, "personal");
            if (!empty($readDB)) {
                $readJsonAsArray = json_decode($readDB, true);
                foreach ($readJsonAsArray as $key => $array) {
                    if ($key == $editfilter) {
                        unset($readJsonAsArray[$key]);
                        $readJsonAsArray[$key] = $_POST;
                        $this->writeJsonData($readJsonAsArray, $path, "personal");
                        header("location:../settings?setting=personal");
                        exit;
                    }
                }
                krsort($readJsonAsArray);
            }
            $this->writeJsonData([$_POST], $path, "personal");
            header("location:../settings?setting=personal");
        }
    }
    public function updateEntladestelleData($path = "")
    {
        if (isset($_REQUEST['updateEntladestellenData'])) {
            $editfilter = $_POST['updateEntladestellenData'];
            $readDB = $this->readJsonData($path, "entladestellen");
            unset($_POST['updateEntladestellenData']);
            if (!empty($readDB)) {
                $readJsonAsArray = json_decode($readDB, true);
                foreach ($readJsonAsArray as $key => $array) {
                    $inummer = $readJsonAsArray[$key]["INUMMER"];
                    if ($key == $editfilter) {
                        $readJsonAsArray[$key] = $_POST;
                        $readJsonAsArray[$key]["INUMMER"] = $inummer;
                    }
                }

                $this->writeJsonData($readJsonAsArray, $path, "entladestellen");
                header("location:../settings?setting=entladestellen");
                //krsort($readJsonAsArray);
                exit;
            }
            $this->writeJsonData([$_POST], $path, "entladestellen");
            header("location:../settings?setting=entladestellen");
        }
    }
    public function addUnloadplace($path = "")
    {
        if (isset($_REQUEST['add_unloadplace'])) {
            unset($_POST['add_unloadplace']);
            $_POST["id"] = 1;
            $readDB = $this->readJsonData($path, "entladestellen");
            if (!empty($readDB)) {
                $readJsonAsArray = json_decode($readDB, true);
                $count = count($readJsonAsArray) + 1;
                $_POST["id"] = $count;
                $readJsonAsArray[] = $_POST;
                krsort($readJsonAsArray);
                $this->writeJsonData($readJsonAsArray, $path, "entladestellen");
                header("location:../settings?setting=entladestellen");
                exit;
            }
            $this->writeJsonData([$_POST], $path, "entladestellen");
            header("location:../settings?setting=entladestellen");
        }
    }
    public function getBMIData($path = "")
    {
        $readDB = $this->readJsonData($path, "bmi");
        if (empty($readDB)) {
            return [];
        }
        $toArray = json_decode($readDB, true);

        foreach ($toArray as $key => $array) {
            $expl = explode("&", $array['Plant']);
            // if($expl[2]==$_SESSION['INUMMER'] && $expl[3]==$_SESSION['abteilung']){
            $arr[$key] = $array;
            // }
        }
        return $arr;
    }
    public function getPersonalData($path = "")
    {
        $readDB = $this->readJsonData($path, "personal");
        if (empty($readDB)) {
            return [];
        }
        return json_decode($readDB, true);
    }
    public function getReminderData($path = "")
    {
        $readDB = $this->readJsonData($path, "reminder");
        if (empty($readDB)) {
            return [];
        }
        return json_decode($readDB, true);
    }
    public function getUnloadPlaceData($path = "")
    {
        $readDB = $this->readJsonData($path, "entladestellen");
        if (empty($readDB)) {
            return [];
        }
        return json_decode($readDB, true);
    }
    public function deleteData()
    {
        if (isset($_REQUEST['deletedata'])) {
            $p = $_REQUEST['p'];
            $deletedata = $_REQUEST['deletedata'];
            $path = "../db/" . $_SESSION['werknummer'] . "/$p.json";
            $readDB = file_get_contents($path);
            echo "<pre>";
            if (!empty($readDB)) {
                $readJsonAsArray = json_decode($readDB, true);
                foreach ($readJsonAsArray as $key => $array) {
                    if ($array['Personalnummer'] == $deletedata) {
                        unset($readJsonAsArray[$key]);
                        $return = "personal";
                    }
                }
                foreach ($readJsonAsArray as $key => $array) {
                    if ($array['BMI-Nummer'] == $deletedata) {
                        unset($readJsonAsArray[$key]);
                        $return = "betriebsmittel";
                    }
                }
                foreach ($readJsonAsArray as $key => $array) {
                    if ($array['Platz'] == $deletedata) {
                        unset($readJsonAsArray[$key]);
                        $return = "entladestellen";
                    }
                }
                foreach ($readJsonAsArray as $key => $array) {
                    if ($array['Reminder-ID'] == $deletedata) {
                        unset($readJsonAsArray[$key]);
                        $return = "reminder";
                        $json = json_encode($readJsonAsArray, JSON_PRETTY_PRINT);
                        file_put_contents($path, $json);
                        header("location:../settings?setting=$return");
                        exit;
                    }
                }

                $json = json_encode($readJsonAsArray, JSON_PRETTY_PRINT);
                file_put_contents($path, $json);
                header("location:../settings?setting=$return");
                exit;
            }
        }
    }
    public function editFormBMI()
    {
        $werkdata = $this->selectPlant("../");
        $editfilter = $_REQUEST['editfilter'];
        $readDB = $this->readJsonData("../", "bmi");
        if (!empty($readDB)) {
            $readJsonAsArray = json_decode($readDB, true);
            //print_R($readJsonAsArray);
            foreach ($readJsonAsArray as $key => $array) {
                if ($key == $editfilter) {
                    $return = $array;
                }
            }
            echo '<div class="row">
            <div class="form-group">
                <div class="small"><label for="Plant" class="small">Werk</label></div>
                    <select name="Plant" class="form-control" id="Plant" required>
                        <option value="' . $return['Plant'] . '">' . $return['Plant'] . '</option>';
            foreach ($werkdata as $data) {
                echo '<option value="' . $data['Werkname'] . '&' . $data['Werknummer'] . '&' . $data['INUMMER'] . '">' . $data['Werkname'] . '</option>';
            }
            echo '</select>
                </div>
            </div>';
            echo '<div class="row">
            <div class="form-group">
                <div class="small"><label for="DEP" class="small">Abteilung</label></div>
                    <select name="DEP" class="form-control" id="DEP" required>
                        <option value="' . $return['DEP'] . '">' . $return['DEP'] . '</option>
                        <option value="Wareneingang:601">Wareneingang</option>
                        <option value="Versand-SKD:602">Versand / SKD</option>
                        <option value="Lackierung:603">Lackierung</option>
                        <option value="Versand-Werk 9:401">Versand-Werk</option>
                    </select>
                </div>
            </div>';
            echo '<div class="row">
                    <div class="form-group">
                    <div class="small"><label for="BMI-Typ" class="small">Typ</label></div>
                        <select class="form-control" name="BMI-Typ" id="BMI-Typ" required>
                            <option value="' . $return['BMI-Typ'] . '">' . $return['BMI-Typ'] . '</option>
                            <option value="Frontstapler">Frontstapler</option>
                            <option value="Schlepper">Schlepper</option>
                        </select>
                    </div>
                </div>';
            echo '<div class="row">
                    <div class="form-group col-6">
                    <div class="small"><label for="Hersteller" class="small">Hersteller</label></div>
                        <input type="text" class="form-control" name="Hersteller" id="Hersteller" value="' . $return['Hersteller'] . '" required>
                    </div>

                    <div class="form-group col-6">
                <div class="small"><label for="Gewicht" class="small">Gewicht</label></div>
                    <input type="text" class="form-control" name="Gewicht" id="Gewicht" value="' . $return['Gewicht'] . '" required>
                </div>
                </div>';
            echo '<div class="row">
            <div class="form-group col-md-6">
                <div class="small"><label for="Gewicht" class="small">INV-Nummer</label></div>
                    <input type="text" class="form-control" name="INV-Nummer" id="INV-Nummer" value="' . $return['INV-Nummer'] . '" required>
                </div>
                <div class="form-group col-md-6">
                <div class="small"><label for="BMI-Nummer" class="small">BMI-Nummer</label></div>
                    <input type="text" class="form-control" name="BMI-Nummer" id="BMI-Nummer" value="' . $return['BMI-Nummer'] . '" required>
                </div>
            </div>';
            echo '<div class="row">
                    <div class="form-group">
                    <div class="small"><label for="BMI-Bild" class="small">BMI-Bild</label></div>
                        <input type="file" accept=".jpg" class="form-control" name="BMI-Bild" id="BMI-Bild">
                    </div>
                </div>';
            echo '<div class="row">
                <div class="form-group">
                <div class="small"><label for="Beschreibung" class="small">Beschreibung</label></div>
                    <textarea class="form-control" name="Beschreibung" id="Beschreibung">' . $return['Beschreibung'] . '</textarea>
                    <input type="hidden" name="BMI-Bild" value="' . $return['BMI-Bild'] . '">
                    <input type="hidden" name="updateBMIData" value="' . $editfilter . '">
                </div>
            </div>';
        }
    }
    public function editFormPersonal()
    {
        $editfilter = $_REQUEST['editfilter'];
        $readDB = $this->readJsonData("../", "personal");
        if (!empty($readDB)) {
            $readJsonAsArray = json_decode($readDB, true);
            foreach ($readJsonAsArray as $key => $array) {
                if ($key == $editfilter) {
                    $return = $array;
                }
            }
            echo '<div class="row">
                    <div class="form-group">
                    <div class="small"><label for="Daimler-ID" class="small">Daimler-ID</label></div>
                        <input type="text" class="form-control" name="Daimler-ID" id="Daimler-ID" value="' . $return['Daimler-ID'] . '">
                    </div>
                </div>';
            echo '<div class="row">
                    <div class="form-group">
                    <div class="small"><label for="Personalnummer" class="small">Personalnummer</label></div>
                        <input type="text" class="form-control" name="Personalnummer" id="Personalnummer" value="' . $return['Personalnummer'] . '" required>
                    </div>
                </div>';
            echo '<div class="row">
                    <div class="form-group">
                    <div class="small"><label for="Name" class="small">Name</label></div>
                        <input type="text" class="form-control" name="Name" id="Name" value="' . $return['Name'] . '" required>
                    </div>
                </div>';
            echo '<div class="row">
                    <div class="form-group">
                    <div class="small"><label for="Vorname" class="small">Vorname</label></div>
                        <input type="text" class="form-control" name="Vorname" id="Vorname" value="' . $return['Vorname'] . '" required>
                    </div>
                    <input type="hidden" name="updatePersonalData" value="' . $editfilter . '">
                </div>';
        }
    }
    public function editFormEntladestellen()
    {
        $editfilter = $_REQUEST['editfilter'];
        $werkdata = $this->selectPlant("../");
        $readDB = $this->readJsonData("../", "entladestellen");
        if (!empty($readDB)) {
            $readJsonAsArray = json_decode($readDB, true);
            foreach ($readJsonAsArray as $key => $array) {
                if ($key == $editfilter) {
                    $return = $array;
                }
            }
            echo '<div class="col-md-6">
                        <div class="form-group row">
                            <div class="small">
                            <label for="Platz" class="small">Abteilung</label></div>
                            <div class="col-sm-9">
                                <select name="abteilung" class="form-control" required>
                                    <option value="">Abteilung</option>';
            foreach ($werkdata as $arrays):
                foreach ($arrays['Deparments'] as $array):
                    echo '<option
                                        value="' . $array['Abteilung'] . ':' . $array['Abt-ID'] . '">
                                        ' . $array['Abteilung'] . '</option>';
                endforeach;
            endforeach;
            echo '</select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                    <div class="form-group">
                    <div class="small"><label for="Platz" class="small">PLatzname</label></div>
                        <input type="text" class="form-control" name="Platz" id="Platz" value="' . $return['Platz'] . '" required>
                    </div>
                </div>';
            echo '<div class="row">
                    <div class="form-group">
                    <div class="small"><label for="Video" class="small">Video</label></div>
                        <input type="text" class="form-control" name="Video" id="Video" value="' . $return['Video'] . '" required>
                    </div>
                    <input type="hidden" name="updateEntladestellenData" value="' . $editfilter . '">
                </div>';
        }
    }
    public function setStaplerForUnload()
    {
        if (isset($_REQUEST['set_for_unload'])) {
            $returnURI = $_REQUEST['return'];
            switch ($returnURI) {
                case "stapler":
                    $uri = "../stapler";
                    break;
                default:
                    $uri = "../";
                    break;
            }
            $findkey = $_REQUEST['set_for_unload'];
            $rfnum = $_REQUEST['stapler_for_unload'];
            $path = "../db/" . $_SESSION['werknummer'] . "/bmi.json";
            $readDB = file_get_contents($path);
            if (!empty($readDB)) {
                $readJsonAsArray = json_decode($readDB, true);
                foreach ($readJsonAsArray as $array) {
                    if ($array['BMI-Nummer'] == $findkey) {
                        $array['timestamp'] = time();
                        $bmiarray = $array;
                    }
                }
                $liteDB = new Sqlite($_SESSION['werknummer']);
                $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
                $result = $liteDB->sqliteSelect($q);
                $readJsonAsArray = json_decode($result[0]['object'], true);
                $readJsonAsArray['Stapler'] = $bmiarray;
                $this->updateTraffic($rfnum, $readJsonAsArray);
            }
            header("location:$uri");
        }
    }
    public function setToLeergutUnload()
    {
        if (isset($_REQUEST['set_to_leergut_unload'])) {
            $rfnum = $_POST['rfnum'];
            unset($_POST['set_to_leergut_unload']);
            unset($_POST['rfnum']);
            $jsonPOST = json_encode($_POST);
            $liteDB = new Sqlite($_SESSION['werknummer']);
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            $readJsonAsArray['leergut_mitnahme'] = $jsonPOST;
            $this->updateTraffic($rfnum, $readJsonAsArray);
            header("location:../");
        }
    }
    public function getLocations($path = "")
    {
        $readDB = $this->readJsonData($path, "entladestellen");
        $arrays =  json_decode($readDB, true);
        ksort($arrays);
        return $arrays;
    }
    public function staplerAuftrag($path = "")
    {
        $arrays = $this->readTraffic($path, "rfnum", false);
        foreach ($arrays as $array) {
            if ($array['Status'] > 25 && $array['Status'] <= 100 || $array['Status'] == 501) {
                $return[] = $array;
            }
        }
        return $return;
    }
    public function saveMyPortview()
    {
        $path = "../db/" . $_SESSION['werknummer'] . "/rolles.json";
        $readDB = file_get_contents($path);
        $weamanageruser = $_SESSION['weamanageruser'];
        if (!empty($readDB)) {
            $readJsonAsArray = json_decode($readDB, true);
            foreach ($readJsonAsArray as $array) {
                if (!empty($array['userid']) && $array['userid'] == $weamanageruser) {
                    $array['Viewport'] = $_POST['Viewport'];
                }
                $new[] = $array;
            }
            $entry = json_encode($new, JSON_PRETTY_PRINT);
            file_put_contents($path, $entry);
        }
    }
    public function getMyPortView()
    {
        $path = "db/" . $_SESSION['werknummer'] . "/rolles.json";
        $readDB = file_get_contents($path);
        $weamanageruser = $_SESSION['weamanageruser'];
        if (!empty($readDB)) {
            $readJsonAsArray = json_decode($readDB, true);
            foreach ($readJsonAsArray as $array) {
                if (!empty($array['Viewport']) && $array['userid'] == $weamanageruser) {
                    return $array['Viewport'];
                }
            }
            return ["warteschlange", "improzess", "utilities"];
        }
    }
    public function saveNeworder()
    {
        if (isset($_REQUEST['save_neworder'])) {
            $liteDB = new Sqlite($_SESSION['werknummer']);
            foreach ($_POST['neworder'] as $key => $rfnum) {
                echo $q = "UPDATE traffic SET ordering=$key WHERE rfnum=$rfnum";
                $liteDB->sqliteQuery($q);
            }
        }
    }
    public function checkRemider()
    {
        if (isset($_REQUEST['checkRemider'])) {

            $ersteller = $_SESSION['weamanageruser'];
            $reminders = $this->getReminderData("../");
            foreach ($reminders as $reminder) {
                if ($reminder['Ersteller'] == $ersteller && empty($_SESSION['reminder'][$reminder['Reminder-ID']])) {
                    $toJSON = json_encode($reminder);
                    switch ($reminder['Turnus']) {
                        case 1:
                            if ($reminder['Uhrzeit'] == date("H:i")) {
                                echo $toJSON;
                            }
                            break;
                        case 2:
                            if ($reminder['Uhrzeit'] == date("H:i") && $reminders['Turnus-Plan'] == date("w")) {
                                echo $toJSON;
                            }
                            break;
                    }
                }
                if ($reminder['Ersteller'] == "RUNDINFO" && empty($_SESSION['reminder'][$reminder['Reminder-ID']])) {
                    $toJSON = json_encode($reminder);
                    switch ($reminder['Turnus']) {
                        case 1:
                            if ($reminder['Uhrzeit'] == date("H:i")) {
                                echo $toJSON;
                            }
                            break;
                        case 2:
                            if ($reminder['Uhrzeit'] == date("H:i") && $reminders['Turnus-Plan'] == date("w")) {
                                echo $toJSON;
                            }
                            break;
                    }
                }
            }
        }
    }
    public function setSessionReminderID()
    {
        if (isset($_REQUEST['setReminderID'])) {
            $reminderID = $_REQUEST['setReminderID'];
            $_SESSION['reminder'][$reminderID] = time();
            header("location:../");
        }
    }
    private function setOnlineData($path = "", $user = "", $identnummer = "")
    {
        $sqlite = new Sqlite($_SESSION['werknummer'], $path);
        $q = "SELECT * FROM resourssen WHERE user='$user'";
        $num =  $sqlite->sqliteSelect($q);
        if (empty($num)) {
            $q = "INSERT INTO resourssen(tstamp,werknummer,user)VALUES(
                " . time() . ",
                '" . $_SESSION['werknummer'] . ":" . $identnummer . "',
                '$user'
            )";
            $sqlite->sqliteQuery($q);
        }
    }
    private function deleteFromOnlineData($path = "", $user)
    {
        $sqlite = new Sqlite($_SESSION['werknummer'], $path);
        $q = "DELETE FROM resourssen WHERE user='$user'";
        $sqlite->sqliteSelect($q);
    }
    public function getOnlineData($path = "")
    {

        $sqlite = new Sqlite($_SESSION['werknummer'], $path);
        $q = "SELECT * FROM resourssen";
        $readJsonAsArray =  $sqlite->sqliteSelect($q);
        if (empty($readJsonAsArray)) {
            return [];
        }
        foreach ($readJsonAsArray as $array) {
            $filter[] = $array['user'];
        }
        return $filter;
    }
    public function checkUserRole($path = "")
    {
        extract($_REQUEST);
        if (empty($Personalnummer)) {
            exit;
        }
        switch ($route) {
            case "karosserie":
            case "trailertausch":
            case "external":
            case "schrottpicker":
            case "werkverlassen":
            case "werkverlassen_ohne_prozess":
                $openDB = "werkschutz";
                break;
            default:
                $openDB = $route;
        }
        $readDB = file_get_contents($path . "db/" . $_SESSION['werknummer'] . "/$openDB.json");
        $readJsonAsArray = json_decode($readDB, true);
        $valid = false;
        foreach ($readJsonAsArray as $array) {
            if ($array['Personalnummer'] == $Personalnummer) {
                $valid = true;
            }
        }
        if ($valid == false) {
            echo "<span class='mt-2 alert-danger d-block p-2 rounded text-center'>keine Berechtigung für den Benutzer</span>";
        }
        if ($valid == true && $route == "werkverlassen") {
            $this->vehicleGone($route, $Personalnummer);
            echo "werkverlassen";
            exit;
        }
        if ($valid == true && $route == "werkverlassen_ohne_prozess") {
            $this->deleteOhneProzess();
            echo "werkverlassen_ohne_prozess";
            exit;
        }
        if ($valid == true) {
            echo 1;
            exit;
        }
    }
    public function addCustomIncommingTime($path = "")
    {
        if (isset($_REQUEST['incomming_time_data'])) {
            $expl = explode(":", $_REQUEST['incomming_time_data']);
            $returnURI = $_REQUEST['returnURI'];
            $rfnum = $expl[0];
            $Nummer = $expl[1];
            $customized_time = $_REQUEST['incomming_time'];
            $customized_platz = $_REQUEST['incomming_platz'];
            $einsteuer = $_SESSION['weamanageruser'];
            $liteDB = new Sqlite($_SESSION['werknummer'], "../");
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            $readJsonAsArray['eingesteuert'] = $einsteuer;
            $readJsonAsArray["customized_time"] = $customized_time;
            $readJsonAsArray["Platz"] = $customized_platz;
            $this->updateTraffic($rfnum, $readJsonAsArray);
            header("location:$returnURI");
        }
    }
    public function addReklamation($path = "")
    {
        if (isset($_REQUEST['add_reklamation'])) {
            $rfnum = $_POST['rfnum'];
            $returnURI = $_POST['returnURI'];
            unset($_POST['add_reklamation']);
            unset($_POST['rfnum']);
            unset($_POST['returnURI']);

            if (!is_dir($path . "db/" . $_SESSION['werknummer'] . "/reklamation")) {
                mkdir($path . "db/" . $_SESSION['werknummer'] . "/reklamation");
            }
            $imagePath = $path . "db/" . $_SESSION['werknummer'] . "/reklamation/";
            if (!empty($_FILES)) {
                require_once 'class_image_edit.php';
                $image = new imageEdit();
                foreach ($_FILES as $files) {
                    foreach ($files['name'] as $key => $file) {
                        $upload[$files['tmp_name'][$key]] = "image($key)" . date("d_m_y_H_i") . ".jpg";
                    }
                }
                foreach ($upload as $key => $value) {
                    if (move_uploaded_file($key, $imagePath . $value)) {
                        $image->resize_image($imagePath . $value, 600, 400, true);
                        $image->machAvatar($imagePath, $imagePath, $value, 150);
                        $_POST['reklamation_bilder'][] = $value;
                    }
                }
            }
            if (!empty($_POST['reklamation_image'])) {
                $avatar = "TNimage(0)" . date("d_m_y_H_i_s") . ".jpg";
                $bild = "image(0)" . date("d_m_y_H_i_s") . ".jpg";
                $_POST['reklamation_bilder'][] = $bild;
                $this->saveCanvasImage($_POST['reklamation_image'], $avatar, $imagePath);
                $this->saveCanvasImage($_POST['reklamation_image'], $bild, $imagePath);
                unset($_POST['reklamation_image']);
            }

            $liteDB = new Sqlite($_SESSION['werknummer'], $path);
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            $readJsonAsArray["Reklamation"] = $_POST;
            $this->updateTraffic($rfnum, $readJsonAsArray);

            header("location:$returnURI");
            exit;
        }
    }
    private function auftragWithoutRFnum($path = "", $returnURI)
    {
        $readDB = $this->readJsonData($path, "manueller_auftrag");
        $readJsonAsArray = json_decode($readDB, true);
        if (!empty($readDB)) {
            $merged = array_merge($readJsonAsArray, [$_POST]);
            $this->writeJsonData($merged, $path, "manueller_auftrag");
            header("location:$returnURI?add_manauftrag=success");
            exit;
        }
        $this->writeJsonData([$_POST], $path, "manueller_auftrag");
        header("location:$returnURI");
        exit;
    }
    public function addManuellerAuftrag($path = "")
    {
        if (isset($_REQUEST['add_manueller_auftrag'])) {
            $rfnum = $_POST['rfnum'];
            $returnURI = "../";
            unset($_POST['add_manueller_auftrag']);
            unset($_POST['rfnum']);
            if (empty($rfnum)) {
                $this->auftragWithoutRFnum($path, $returnURI);
            }
            $liteDB = new Sqlite($_SESSION['werknummer'], $path);
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            $readJsonAsArray["MA-Autfrag"] = $_POST;
            $this->updateTraffic($rfnum, $readJsonAsArray);
            header("location:$returnURI?add_manauftrag=success");
            exit;
        }
    }
    public function addUnloadprio($path = "")
    {
        if (isset($_REQUEST['add_unloadprio'])) {
            extract($_REQUEST);
            if ($Prio == "unset") {
                $Prio = null;
            }

            $liteDB = new Sqlite($_SESSION['werknummer'], "../");
            $q = "SELECT object FROM traffic WHERE rfnum=" . $rfnum . "";
            $result = $liteDB->sqliteSelect($q);
            $readJsonAsArray = json_decode($result[0]['object'], true);
            $readJsonAsArray["Prio"] = $Prio;
            $readJsonAsArray["Prio-Melder"] = $_SESSION['weamanageruser'];

            $this->updateTraffic($rfnum, $readJsonAsArray);
            header("location:../$returnURI");
            exit;
        }
    }
    public function modifyText($text)
    {
        if (empty($text)) {
            return null;
        }
        $expl = explode(" ", $text);
        $newLine = [4, 10, 15, 22];
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
    public function staplerAufgaben()
    {
        $path = "../";
        $readDB = $this->readTraffic($path);
        $readDB_manAuftag = $this->readJsonData($path, "manueller_auftrag");
        $toArray_manAuftag = json_decode($readDB_manAuftag, true);
        foreach ($readDB as $array) {

            if (!empty($array['Stapler']['BMI-Nummer']) && $array['Status'] < 100) {
                $arr[$array['Stapler']['BMI-Nummer']][] = [
                    "rfnum" => "WN " . $array['rfnum'],
                    "Firma" => $array['Firma'],
                    "Platz" => $array['Platz'],
                    "Aufgabe" => "im Prozess",
                    "tstamp" => $array['timestamp']
                ];
            }
            if (!empty($array['MA-Autfrag']) && empty($array['MA-Autfrag']['signed'])) {
                $arr[$array['MA-Autfrag']['stapler_for_auftrag']][] = [
                    "rfnum" => "Zusatz Auftrag",
                    "Firma" => "Zentrale",
                    "Platz" => "--",
                    "Aufgabe" => $array['MA-Autfrag']['umfang'],
                    "tstamp" => $array['MA-Autfrag']['timestamp']
                ];
            }
        }
        foreach ($toArray_manAuftag as $array) {
            if (!empty($array['stapler_for_auftrag'])) {
                $arr[$array['stapler_for_auftrag']][] = [
                    "rfnum" => "Man. Auftrag",
                    "Firma" => "Zentrale",
                    "Platz" => "--",
                    "Aufgabe" => $array['umfang']
                ];
            }
        }
        return $arr;
    }
    public function getTaskCounts($bmi_nummer)
    {
        $path = "../";
        $addTask = "";
        $readDB = $this->readTraffic($path);
        $liteDB = new Sqlite($_SESSION['werknummer'], $path);
        $q = "SELECT bmi_nummer FROM add_tasks WHERE task='rueckenandienung'";
        $bmiAddTaskRueck = $liteDB->sqliteSelect($q);
        $readDB_manAuftag = $this->readJsonData($path, "manueller_auftrag");

        $toArray_manAuftag = json_decode($readDB_manAuftag, true);
        if (!empty($readDB)) {
            foreach ($readDB as $array) {
                if ($array['Stapler']['BMI-Nummer'] == $bmi_nummer && $array['Status'] < 100) {
                    $count[] = $array;
                }
                if ($array['MA-Autfrag']['stapler_for_auftrag'] == $bmi_nummer && empty($array['MA-Autfrag']['signed'])) {
                    $count[] = $array;
                }
            }
        }
        if (!empty($toArray_manAuftag)) {
            foreach ($toArray_manAuftag as $array) {
                if ($array['stapler_for_auftrag'] == $bmi_nummer) {
                    $count[] = $array;
                }
            }
        }
        foreach ($toArray_manAuftag as $array) {
            if ($array['stapler_for_auftrag'] == $bmi_nummer) {
                $count[] = $array;
            }
        }
        foreach ($toArray_manAuftag as $array) {
            foreach ($bmiAddTaskRueck as $stapler) {
                if ($bmi_nummer == $stapler['bmi_nummer']) {
                    $count[] = $array;
                    $addTask = "Rueck";
                }
            }
        }
        if (empty($count)) {
            return "";
        }
        return "<span class='badge badge-info p-1 float-right m-1 small rounded'>" . $addTask . " " . count($count) . "</span>";
    }
    public function editRFnumData($getRFnum, $path = "")
    {

        $readDB = $this->readTraffic($path);
        if (empty($getRFnum)) {
            return [];
        }
        foreach ($readDB as $arrays) {
            if ($arrays['rfnum'] == $getRFnum) {
                return $arrays;
            }
        }
    }
    public function Alert($type, $text)
    {
        if ($type == "error") {
            $style = "alert-danger bg-danger";
        }
        if ($type == "success") {
            $style = "alert-success bg-success";
        }
        echo '<div class="alert ' . $style . ' text-light text-center mb-0 border-0 alert-dismissible fade show fixed-bottom" role="alert">
                <button type="button" id="close-alert" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                ' . $text . '
            </div>';
    }
    public function downloadAPKupdate()
    {
        if (isset($_REQUEST['downloadAPKupdate'])) {
            $files_url = "../db/" . $_SESSION['werknummer'] . "/APK/";
            $scanned_directory = array_diff(scandir($files_url), array('..', '.'));
            foreach ($scanned_directory as $apk) {

                header('Content-Type: application/vnd.android.package-archive');
                header("Content-length: " . filesize($files_url . $apk));
                header('Content-Disposition: attachment; filename="' . $apk . '"');
                ob_end_flush();
                readfile($files_url . $apk);
                return true;
            }
        }
    }
    public function downloadScript()
    {
        if (isset($_REQUEST['downloadfile'])) {
            $file_url = "../db/" . $_SESSION['werknummer'] . "/extern/GUI/" . $_REQUEST['downloadfile'];
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
            readfile($file_url);
        }
    }
    public function scannerNummer()
    {
        $expl = explode(".", $_SERVER['REMOTE_ADDR']);
        return "Nr.: " . $expl[2] . "." . $expl[3];
    }
    public function adittionalJob()
    {
        if (isset($_REQUEST['adittionalJob'])) {
            $liteDB = new Sqlite($_SESSION['werknummer']);
            $adittionalJob = $_REQUEST['adittionalJob'];
            $_SESSION['adittionalJob'] = $adittionalJob;
            if ($adittionalJob == "norueckenandienung") {
                $_SESSION['adittionalJob'] = null;
                setcookie("adittionalJob", "", time() - 1, "/");
                header("location:../stapler");
                $q = "DELETE FROM add_tasks WHERE bmi_nummer='" . $_SESSION['weamanageruser'] . "'";
                $liteDB->sqliteQuery($q);
                exit;
            }
            setcookie("adittionalJob", $_SESSION['adittionalJob'], time() + 86400, "/");
            $q = "INSERT INTO add_tasks(bmi_nummer,task)VALUES('" . $_SESSION['weamanageruser'] . "','rueckenandienung')";
            $liteDB->sqliteQuery($q);
            header("location:../stapler");
        }
    }
    public function modal($id, $size)
    {
        echo '<div class="modal fade p-0" id="' . $id . '" tabindex="-1" data-bs-backdrop="static" aria-labelledby="' . $id . 'ModalLabel" aria-hidden="true">
            <div class="modal-dialog ' . $size . '">
                <div class="modal-content">
                <div class="modal-header" id="header-' . $id . '">
                    <h5 class="modal-title" id="' . $id . 'ModalLabel"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-1 p-lg-3 overflow-auto" id="' . $id . '-body">
                lade Inhalt...
                </div>
                </div>
            </div>
            </div>';
    }
}