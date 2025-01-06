<?php

class StaplerAufgaben
{

    private function getAufgabeData()
    {
        $readDB = file_get_contents("../../../NU/shipping/db/aufgaben.json");
        return json_decode($readDB, true);
    }
    private function getSeqLagerStucrute()
    {
        $readDB = file_get_contents("../../../NU/shipping/db/seqlager-structure.json");
        return json_decode($readDB, true);
    }
    private function getBoxenAfterLack()
    {
        $db = new connect();
        $q = "SELECT BB, boxnummer, done, werk FROM offline_lackierung.dbo.monitor_lack WHERE done !='' ORDER BY id DESC";
        $result = $db->select($q);
        foreach ($result as $arrays) {
            if (!empty($arrays['boxnummer'])) {
                $array[$arrays['werk'] . "&" . $arrays['BB']][$arrays['boxnummer']] = $arrays['done'];
            }
        }
        return $array;
    }
    private function isInSeqLager($BB, $boxnummer)
    {
        $db = new connect();
        $q = "SELECT id,platzname FROM offline_lackierung.dbo.monitor_seqlager WHERE BB='$BB' AND lt_nummer='$boxnummer'";
        $result = $db->select($q);
        if (!empty($result[0])) {
            return $result[0];
            exit;
        }
        return null;
    }
    private function belegungInSeqLager($platzname)
    {
        $db = new connect();
        $q = "SELECT BB, lt_nummer FROM offline_lackierung.dbo.monitor_seqlager WHERE platzname='$platzname'";
        return $db->select($q);
    }
    public function addToSeqLager()
    {
        if (isset($_REQUEST['addToSeqLager'])) {
            $db = new connect();
            $BB = $_REQUEST['BB'];
            $platzname = $_REQUEST['platzname'];
            $lt_nummer = $_REQUEST['boxnummer'];
            $werk = $_REQUEST['werk'];
            $isInSeqLager = $this->isInSeqLager($BB, $lt_nummer);
            if (!empty($BB) && empty($isInSeqLager)) {
                $q = "INSERT INTO offline_lackierung.dbo.monitor_seqlager(tstamp, BB, platzname, lt_nummer,werk)VALUES(
                    " . time() . ",
                    '$BB',
                    '$platzname',
                    '$lt_nummer',
                    '$werk'
                )";
                $res = $db->query($q);
                if ($res > 0) {
                    header("location: ../stapler?success=add-to-seqlager");
                    exit;
                }
            }
            if (!empty($BB) && !empty($isInSeqLager)) {
                $q = "UPDATE offline_lackierung.dbo.monitor_seqlager SET platzname='$platzname' WHERE id=" . $isInSeqLager['id'] . "";
                $res = $db->query($q);
                if ($res > 0) {
                    header("location: ../stapler?success=update-in-seqlager");
                    exit;
                }
            }
            header("location: ../stapler?error=add-to-seqlager");
        }
    }
    public function meineAufgaben($staplernummer, $inummer = "")
    {
        $arrays = $this->getAufgabeData();
        foreach ($arrays as $datum => $aufgabe) {
            foreach ($aufgabe as $BB => $array) {
                $arrayAufgabe[$BB] = $array;
            }
        }
        $arrayAfterLack = $this->getBoxenAfterLack();
        $this->cardAufgabe($arrayAfterLack, $arrayAufgabe);
    }
    public function setLKWnummerToSession()
    {
        session_start();
        if (isset($_REQUEST['set_LKW_nummer'])) {
            $lkw = $_REQUEST['set_LKW_nummer'];
            $lkwnummer = $_REQUEST['LKW_nummer'];
            $targetWerk = $_REQUEST['target_werk'];

            $_SESSION['offline_verladeprozess'][$lkw . "&" . $targetWerk] = $lkwnummer;
            header("location:../stapler");
        }
    }
    public function offlineLoad()
    {
        if (isset($_REQUEST['offline_load_box'])) {
            $db = new connect();
            $tb = "offline_lackierung.dbo.monitor_lkw";
            $fpath = "../../../NU/shipping/db/aufgaben.json";
            $boxnummer = $_REQUEST['offline_load_box'];
            $BB = $_REQUEST['BB'];
            $lkw = $_REQUEST['lkw'];
            $readDB = file_get_contents($fpath);
            $toArray = json_decode($readDB, true);
            foreach ($toArray as $datum => $arrays) {
                foreach ($arrays as $key => $array) {
                    $expl = explode("&", $key);
                    $stapler = $expl[0];
                    $data  = explode("-", $expl[1]);
                    $werk = $data[1];
                    foreach ($array as $i => $item) {

                        if ($item['BB'] == $BB && $boxnummer == $item['lt_nummer']) {
                            $q = "SELECT id FROM $tb WHERE BB='$BB' AND boxnummer='$boxnummer'";
                            $num = $db->numrows($q);
                            if ($num > 0) {
                                $q = "UPDATE $tb SET 
                                knz='$lkw', werk='$werk', datum='" . date("d.m.y") . "', boxnummer='$boxnummer', stapler='$stapler', tstamp=" . time() . ", BB='$BB'
                                WHERE BB='$BB' AND boxnummer='$boxnummer'";
                            }
                            if ($num < 1) {
                                $q = "INSERT INTO $tb(knz,werk,datum,boxnummer,stapler,tstamp,BB)VALUES(
                                    '$lkw',
                                    '$werk',
                                    '" . date("d.m.y") . "',
                                    '$boxnummer',
                                    '$stapler',
                                    " . time() . ",
                                    '$BB'
                                )";
                            }
                            $res = $db->query($q);
                            if ($res > 0) {
                                $item['verladen'] = time();
                                $item['lkw'] = $lkw;
                                $item['ist_platz'] = $lkw;
                                $toArray[$datum][$key][$i] = $item;
                                $toJson = json_encode($toArray, JSON_PRETTY_PRINT);
                                file_put_contents($fpath, $toJson);
                            }
                            header("location:../stapler");
                            exit;
                        }
                    }
                }
            }
        }
    }
    public function offlineversandBuchen()
    {
        if (isset($_REQUEST['offlineversand_buchen'])) {
            $db = new connect();
            $werk = $_REQUEST['versand_werk'];
            $lkw = $_REQUEST['versand_lkw'];
            $BB = $_REQUEST['versand_BB'];
            $boxnummerArray = $_REQUEST['versand_boxnummer'];

            $stapler = $_SESSION['weamanageruser'];
            $tb = "offline_lackierung.dbo.monitor_lkw";
            $tbSeqLager = "offline_lackierung.dbo.monitor_seqlager";
            $tbLackierung = "offline_lackierung.dbo.monitor_lack";

            foreach ($boxnummerArray as $boxnummer) {
                if (!empty($boxnummer)) {

                    $q = "SELECT TOP 1 BB,werk,boxnummer,teilegruppe FROM $tbLackierung WHERE  boxnummer='$boxnummer' ORDER BY tstamp DESC";
                    $result = $db->select($q);

                    if (empty($result[0]['BB'])) {
                        $q = "SELECT TOP 1 BB,werk,lt_nummer,ug FROM $tbSeqLager WHERE  lt_nummer='$boxnummer' ORDER BY tstamp DESC";
                        $result = $db->select($q);
                    }
                    if (empty($result[0]['BB'])) {
                        echo "<span class='alert bg-danger text-light m-2 p-1 text-center d-block'>
                            $boxnummer Ladeträger nicht gefunden!</span>";
                        continue;
                    }
                    if (empty($result[0]['werk'])) {
                        echo "<span class='alert bg-danger text-light m-2 p-1 text-center d-block'>
                        Zielwerk ist nicht in Datenbank für $boxnummer</span>";
                        continue;
                    }
                    if ($result[0]['werk'] != $werk) {
                        echo "<span class='alert bg-danger text-light m-2 p-1 text-center d-block'>
                        Zielwerk " . $result[0]['werk'] . " ist falsch für $boxnummer</span>";
                        continue;
                    }
                    if (!empty($result[0]['BB'])) {
                        $tg = [];
                        $BB = $result[0]['BB'];
                        $werk = $result[0]['werk'];
                        $boxnummer = $result[0]['boxnummer'];
                        foreach ($result as $tmp) {
                            $tg[] = $tmp['teilegruppe'];
                        }
                        $teilegruppe = implode(",", $tg);
                    }
                    $q = "SELECT id FROM $tb WHERE BB='$BB' AND boxnummer='$boxnummer'";
                    $num = $db->numrows($q);
                    if ($num < 1) {
                        $res = $db->select("SELECT id FROM $tbSeqLager ORDER BY id DESC");
                        $seID = $res[0]['id'] + 1;
                        $q = "INSERT INTO $tb(lkw,werk,datum,boxnummer,stapler,tstamp,BB,seqid,ug)VALUES(
                            '$lkw',
                            '$werk',
                            '" . date("d.m.Y") . "',
                            '$boxnummer',
                            '$stapler',
                            " . time() . ",
                            '$BB',
                            " . $seID . ",
                            '$teilegruppe'
                        )";
                        $res = $db->query($q);
                    }
                    if ($num == 1) {
                        $q = "UPDATE $tb SET 
                        lkw='$lkw',
                        werk = '$werk',
                        datum='" . date("d.m.Y") . "',
                        boxnummer = '$boxnummer',
                        stapler='$stapler', tstamp=" . time() . " WHERE BB='$BB'";
                        echo "<span class='alert bg-success text-light m-2 p-1 text-center d-block'>
                        $boxnummer erfolgreich upgedated!
                        </span>";
                    }
                    if ($res == 1) {
                        $q2 = "DELETE FROM $tbSeqLager WHERE lt_nummer='$boxnummer'";
                        $db->query($q2);
                        echo "<span class='alert bg-success text-light m-2 p-1 text-center d-block'>
                        $boxnummer erfolgreich gebucht!
                        </span>";
                    }
                }
            }
        }
    }
    public function inhoflagerBuchen()
    {
        if (isset($_REQUEST['inhoflager_buchen'])) {
            $db = new connect();
            $platzname = $_REQUEST['hoflager'];
            $BB = $_REQUEST['BB'];
            $lt_nummer = $_REQUEST['boxnummer'];
            $stapler = $_SESSION['weamanageruser'];
            $tbHofLager = "offline_lackierung.dbo.monitor_seqlager";
            $tbLackierung = "offline_lackierung.dbo.monitor_lack";
            $q = "SELECT BB,werk,boxnummer FROM $tbLackierung WHERE boxnummer='$lt_nummer'";
            $result = $db->select($q);

            if (empty($result[0]['BB'])) {
                echo "Ladeträger nicht gefunden! ";
                exit;
            }
            if (!empty($result[0]['BB'])) {
                $BB = $result[0]['BB'];
                $werk = $result[0]['werk'];
                $lt_nummer = $result[0]['boxnummer'];
            }
            $q = "SELECT id FROM $tbHofLager WHERE BB='$BB' AND lt_nummer='$lt_nummer'";
            $num = $db->numrows($q);
            if ($num < 1) {
                $q = "INSERT INTO $tbHofLager(tstamp,BB,platzname,lt_nummer,werk,stapler)VALUES(
                    '" . time() . "',
                    '$BB',
                    '$platzname',
                    '$lt_nummer',
                    '$werk',
                    '$stapler'
                )";
            }
            if ($num > 0) {
                $q = "UPDATE $tbHofLager SET 
                tstamp='" . time() . "',
                platzname='$platzname',
                stapler='$stapler' WHERE BB='$BB' AND lt_nummer='$lt_nummer'";
            }
            echo $db->query($q);
        }
    }
    public function auftragFertig()
    {
        if (isset($_REQUEST['auftrag_fertig'])) {
            $auftrag_fertig = $_REQUEST['auftrag_fertig'];
            $fpath = "../../../NU/shipping/db/aufgaben.json";
            echo "<pre>";
            $readDB = file_get_contents($fpath);
            $toArray = json_decode($readDB, true);
            foreach ($toArray as $datum => $arrays) {
                foreach ($arrays as $key => $array) {
                    if ($auftrag_fertig == $key) {
                        unset($toArray[$datum][$key]);
                        if (empty($toArray[$datum])) {
                            $toArray = [];
                        }
                        $toArray = json_encode($toArray, JSON_PRETTY_PRINT);
                        file_put_contents($fpath, $toArray);
                        exit;
                    }
                }
            }
        }
    }
    private function versandInformation($BB)
    {
        $db = new connect();
        $tb = "offline_lackierung.dbo.monitor_lkw";
        $qLkw = "SELECT * FROM $tb WHERE BB = '$BB'";
        $tb = "offline_lackierung.dbo.monitor_seqlager";
        $qSeqLager = "SELECT * FROM $tb WHERE BB = '$BB'";
        $countSeqLager = $db->numrows($qSeqLager);
        $countLkw = $db->numrows($qLkw);
        if ($countSeqLager > 0 && $countSeqLager == $countLkw) {
            return true;
        }
        return false;
    }
    private function countsInSeqlager($BB = "", $hoflager = "", $total = false)
    {
        $db = new connect();
        if (empty($hoflager) && $total == true) {
            $tb = "offline_lackierung.dbo.monitor_seqlager";
            $q = "SELECT * FROM $tb WHERE platzname !=''";
            return $db->numrows($q);
        }
        if (!empty($hoflager) && $total == true) {
            $tb = "offline_lackierung.dbo.monitor_seqlager";
            $q = "SELECT * FROM $tb WHERE platzname ='$hoflager'";
            return $db->numrows($q);
        }
        $tb = "offline_lackierung.dbo.monitor_seqlager";
        $q = "SELECT * FROM $tb WHERE BB='$BB'";
        return $db->numrows($q);
    }
    private function cardAufgabe($arrayAfterLack, $arrayAufgabe)
    {
        $countsInSeqLager = $this->countsInSeqlager("", "", true);
        $countsInHof1 = $this->countsInSeqlager("", "LW 1", true);
        $countsInHof2 = $this->countsInSeqlager("", "LW 2", true);
        $return = '<table class="table">
            <tr><td>
            <div class="row">';
        $return .= '<div class="col-md-6 p-1">
            <div class="card min-height-10">
                <div class="card-header h5 p-1">Lagerübersicht</div>
                    <div class="card-body p-1 max-height-70-vh overflow-auto">';
        $return .= '<table class="table">';
        $return .= '
                        <tr class="">
                            <td class="text-center border-0">
                                <div class="row justify-content-center">
                                    <div class="col-md-9 p-2 text-center">
                                    <button class="btn-lg width-20 btn-secondary border open-seqLager fs-5 p-3" alt="" data-bs-toggle="modal" data-bs-target="#openSeqLagerFull">
                                        SequenzLager
                                    <span class="badge badge-secondary float-end">' . $countsInSeqLager . '</span> 
                                    </button>
                                    </div>
                                    <div class="col-md-9 p-2 text-center">
                                    <button class="btn-lg width-20 btn-secondary border open-hoflager fs-5 p-3" alt="" data-bs-toggle="modal" data-bs-target="#startBeladen">
                                        Lack-Wand 1
                                    <span class="badge badge-secondary float-end">' . $countsInHof1 . '</span> 
                                    </button>
                                    </div>
                                    <div class="col-md-9 p-2 text-center">
                                    <button class="btn-lg width-20 btn-secondary border open-hoflager fs-5 p-3" alt="" data-bs-toggle="modal" data-bs-target="#startBeladen">
                                        Lack-Wand 2
                                    <span class="badge badge-secondary float-end">' . $countsInHof2 . '</span> 
                                    </button>
                                    </div>
                                    <div class="col-md-9 p-2 text-center">
                                    <button class="btn-lg width-20 btn-secondary border open-start-beladen fs-5 p-3" alt="" data-bs-toggle="modal" data-bs-target="#startBeladen">
                                        LKW Laden
                                    </button>
                                    </div>
                                    <div class="col-md-9 p-2 text-center">
                                    <button class="btn-lg width-20 btn-secondary border open-check-inhalt fs-5 p-3" alt="" data-bs-toggle="modal" data-bs-target="#checkInhalt">
                                        Check Inhalt
                                    </button>
                                    </div>
                                </div>
                            </td>
                        </tr>';
        $return .= '</table>';
        $return .= '</div>
            </div>
        </div>';
        $return .= '<div class="col-md-6 p-1">
        <div class="card min-height-10">
            <div class="card-header h5 p-1">Versand heute <button class="btn btn-sm p-1 me-2 btn-primary float-end" data-index="' . date("Y-m-d") . '" id="request-versand-list">aktuell</button></div>
                <div class="card-body p-1 max-height-70-vh overflow-auto">
                <h2>Mannheim</h2>';
        if (!empty($arrayAufgabe)) {
            $return .= '<table class="table column-table mb-4">';
            foreach ($arrayAufgabe as $BB => $aufgaben) {
                $styleRow = null;
                $completeOnTruck = $this->versandInformation($BB);
                $countRows = $this->countsInSeqlager($BB, false);
                if ($completeOnTruck == true) {
                    $styleRow = "bg-success text-light";
                }
                $return .= '<tr class="tr-hover ' . $styleRow . '">
                            <td class="h6 pt-2 pb-2 align-middle">' . $BB . '</td><td class="align-middle"><span class="badge badge-primary"></span></td>
                             <td class="h6 pt-2 pb-2 text-end">
                             <button alt="' . $BB . '" class="btn btn-primary open-seqLager" data-bs-toggle="modal" data-bs-target="#openSeqLagerFull">
                                finden
                             </button></td>
                        </tr>';
            }
            $return .= '<table>';
        }
        $return .= '</div>
        </div>';
        $return .= '</div>';
        $return .= '</div></td></tr></table>';
        echo $return;
    }
    public function modalExtern($id, $size = "")
    {
        echo '<div class="modal fade" id="' . $id . '" data-bs-backdrop="static" tabindex="-1" aria-labelledby="Label" aria-hidden="true">
        <div class="modal-dialog ' . $size . '">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0 pt-1">
                <iframe src="content/wificheck" width="60px" height="40px"></iframe>
                    <button type="button" class="btn-close bg-secondary rounded text-light" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modal-body-' . $id . '">
                </div>
            </div>
        </div>
    </div>';
    }
    public function openStartBeladen()
    {
        if (isset($_REQUEST['openStartBeladen'])) {
            $_SESSION['konfigurator'] = $_SESSION['weamanageruser'];
            echo '<div class="row">
            <div class="col-md-6 p-0">
                        <form action="class/publicExtern" method="post" id="versand_buchen">        
                <div class="row mb-1">
                        <label class="col-sm-3 col-form-label pb-0 mb-0">Werk</label>
                        <div class="col-sm-9">
                        <select class="form-select" name="versand_werk" aria-label="Werk select" id="versand_werk" required>
                            <option value="">Werk</option>
                            <option value="0028">Mannheim</option>
                            <option value="0062">Ligny</option>
                        </select>
                        </div>
                    </div>
                    <div class="row mb-1">
                        <label class="col-sm-3 col-form-label pb-0 mb-0">LKW ID</label>
                        <div class="col-sm-9">
                        <select class="form-select" name="versand_lkw" id="versand_lkw" aria-label="LKW select" required>
                            <option value="">LKW</option>
                            <option value="A">LKW A</option>
                            <option value="B">LKW B</option>
                            <option value="C">LKW C</option>
                        </select>
                        </div>
                    </div>';
            echo '<div class="row mb-3" id="first-scannLT-row">
                        <label for="inputText" class="col-sm-3 col-form-label pb-0 mb-0">LT-Nummer</label>
                        <div class="col-7">
                            <input type="text" name="versand_boxnummer[]" id="scannLT" class="form-control mb-2">
                        </div>
                        <div class="col-2">
                            <button type="button" class="font-large btn btn-secondary open-reader btn-rounded btn-icon" alt="LT"><i class="fas fa-qrcode"></i></button>
                        </div>
                    </div>';
            //echo "<div class='row' id='scans'></div>";
            echo '<div class="row mt-2 mb-3">
                        <label class="col-sm-3 col-form-label">Buchen</label>
                        <div class="col-sm-9">
                        <input type="hidden" name="offlineversand_buchen" value="1">
                            <button type="submit" id="alert-offlineversand_buchen" class="btn btn-primary snd-btn">Buchen</button>
                            <button type="button" id="reset-frame" class="btn btn-warning p-2 snd-btn float-end"><i class="ti-reload"></i></button>
                        </div>
                    </div>
                </form>
                <div class="alert alert-success p-2 d-none text-center" alt="erfolgreich gebucht!" id="success-alert-versand_buchen"></div>

            </div>
             <div class="col-md-6">
                    <iframe src="https://' . $_SERVER['HTTP_HOST'] . '/NU/shipping/?open=truckload" id="iframe-lkw-loading"></iframe>
                </div>
            </div>';
        }
    }
    public function openHoflagerBeladen()
    {
        if (isset($_REQUEST['openHoflagerBeladen'])) {
            echo '<div class="row">
                <div class="col-md-6">
                   <form action="class/publicExtern" method="post" id="inhoflager_buchen">
                <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Hoflager</label>
                        <div class="col-sm-9">
                        <select class="form-select" name="hoflager" aria-label="Werk select" required>
                            <option value="">wählen</option>
                            <option value="LW 1">LW 1</option>
                            <option value="LW 2">LW 2</option>
                        </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputText" class="col-12 col-form-label">BB</label>
                        <div class="col-10">
                            <input type="text" name="BB" id="scannBB" class="form-control">
                        </div>
                        <div class="col-2">
                            <button type="button" class="font-large btn btn-secondary btn-rounded btn-icon open-reader" alt="BB"><i class="fas fa-qrcode"></i></button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputText" class="col-12 col-form-label">LT-Nummer</label>
                        <div class="col-10">
                            <input type="text" name="boxnummer" id="scannLT" class="form-control" required>
                        </div>
                        <div class="col-2">
                            <button type="button" class="font-large btn btn-secondary open-reader btn-rounded btn-icon" alt="LT"><i class="fas fa-qrcode"></i></button>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label">Buchen</label>
                        <div class="col-9">
                        <input type="hidden" name="inhoflager_buchen" value="1">
                            <button type="submit" id="alert-inhoflager_buchen" class="btn btn-primary snd-btn">Buchen</button>
                        </div>
                    </div>
                </form>
                </div>
            </div>
            <div class="alert alert-success p-2 d-none text-center" id="success-alert-inhoflager_buchen">erfolgreich gebucht</div>';
        }
    }
    public function sequenzierungsLager()
    {
        if (isset($_REQUEST['openSeqlagerFull'])) {
            $_SESSION['konfigurator'] = $_SESSION['weamanageruser'];
            $searchBB = null;
            if (isset($_REQUEST['searchBB']) && !empty($_REQUEST['searchBB'])) {
                $searchBB = "?searchBB=" . $_REQUEST['searchBB'];
            }
            echo '<iframe src="https://' . $_SERVER['HTTP_HOST'] . '/NU/shipping/' . $searchBB . '" style="width:100%; height:105vh;"></iframe>';
        }
    }
    public function openPlatzBelegung()
    {

        if (isset($_REQUEST['openPlatzBelegung'])) {
            $platzname = $_REQUEST['openPlatzBelegung'];
            $expl = explode("&", $_REQUEST['boxdata']); //BB6286410229&60516-001 
            $BB = $expl[0];
            $werk = $_REQUEST['werk'];
            $boxnummer = $expl[1];
            echo '<form action="class/publicExtern">
                    <div>
                         <span class="h2">' . $platzname . ' <br> ' . $BB . ', ' . $boxnummer . '</span> 
                    </div>
                    <div class="card-footer text-end p-0 pt-2">
                    <input type="hidden" name="platzname" value="' . $platzname . '">
                    <input type="hidden" name="BB" value="' . $BB . '">
                    <input type="hidden" name="werk" value="' . $werk . '">
                    <input type="hidden" name="boxnummer" value="' . $boxnummer . '">
                    <input type="hidden" name="addToSeqLager" value="1">
                            <button type="submit" class="btn btn-secondary">ok</button>
                    </div>
            </form>';
        }
    }
}