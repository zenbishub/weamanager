<?php

class GabischCollector
{

    private $db;
    private $sqlite;
    private $database;
    private $werk;
    public function __construct()
    {
        $this->setAppData();
    }
    private function setAppData()
    {
        $appPath = __dir__ . "/app.json";
        if (!file_exists($appPath)) {
            die("Can not reach App Config. Check app.json");
        }
        $app = json_decode(file_get_contents($appPath));
        $this->werk = $app->appdata->werknummer;
        $this->database = $app->appdata->database;
        require_once 'Connect.php';
        require_once 'Sqlite.php';
        $this->db = new connect();
        $this->sqlite = new Sqlite($this->werk);
    }
    private function deleteOldMessages($path = "", $rfnum = "", $range = "")
    {
        // Löschen Eintäge in Chat, die älter als Range
        $readDB = file_get_contents($path . "db/" . $this->werk . "/conversation.json");
        $arrays = json_decode($readDB, true);
        $arrChat = [];
        if (!empty($rfnum)) {
            foreach ($arrays as $value) {
                if ($value['rfnum'] != $rfnum) {
                    $arrChat[] = $value;
                }
            }
            $entry = json_encode($arrChat, JSON_PRETTY_PRINT);
            file_put_contents($path . "db/" . $this->werk . "/conversation.json", $entry);
        }
        if (empty($rfnum)) {
            foreach ($arrays as $value) {
                if ($value['sendtime'] > $range) {
                    $arrChat[] = $value;
                }
            }
            $entry = json_encode($arrChat, JSON_PRETTY_PRINT);
            file_put_contents($path . "db/" . $this->werk . "/conversation.json", $entry);
        }
    }
    private function deletePersonenDatenAusProtokoll()
    {
        $werknummer = $this->werk;
        $range = time() - 86400;
        $q = "SELECT TOP 50 id, object_runleaf FROM " . $this->database . ".dbo.werk_$werknummer WHERE tstamp<$range";
        $temp = $this->db->select($q);
        if (empty($temp)) {
            return [];
        }
        foreach ($temp as $value) {
            $unset = json_decode($value['object_runleaf'], true);
            unset($unset['Name Fahrer']);
            unset($unset['Legitimation']);
            $unseted = json_encode($unset);
            $q = "UPDATE " . $this->database . ".dbo.werk_$werknummer SET object_runleaf='$unseted' WHERE id=" . $value['id'] . "";
            $this->db->query($q);
        }
    }
    public function gabishCollector($path = "", $timer = "")
    {
        $range = time() - (86400 * 60); // 60 Tage
        $deleteFromDB = time() - 63072000; // 2 Jahre
        // Automatische Löschung durch Timer
        if (!empty($timer) && $timer == "on") {
            $timeArray = [
                "00:01",
                "01:01",
                "02:01",
                "03:01"
            ];

            if (date("H:i") == "00:01" || in_array(date("H:i"), $timeArray)) {
                file_put_contents($path . "db/" . $this->werk . "/conversation.json", null);
                file_put_contents($path . "db/" . $this->werk . "/chat/evochat.json", null);
                file_put_contents($path . "db/" . $this->werk . "/scannerstatus.json", null);
                file_put_contents($path . "db/" . $this->werk . "/manueller_auftrag.json", null);
                $this->db->query("DELETE FROM werk_" . $this->werk . " WHERE tstamp<$deleteFromDB");
                $this->sqlite->sqliteQuery("DELETE FROM traffic");
                $this->sqlite->sqliteQuery("DELETE FROM useronline");
                $this->sqlite->sqliteQuery("DELETE FROM resourssen");
            }
        }
        // Löschen Bilder und Eintäge im Verlauf, die älter als Range
        $directory = $path . "db/" . $this->werk . "/img_temp/";
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));

        foreach ($scanned_directory as $file) {
            $checkFile = filemtime($directory . $file);
            if ($checkFile < $range) {
                unlink($directory . $file);
            }
        }
        // löschen alte EvoChatBilder
        $directory = $path . "db/" . $this->werk . "/chat/image_chat/";
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));

        foreach ($scanned_directory as $file) {
            $checkFile = filemtime($directory . $file);
            if ($checkFile < $range) {
                unlink($directory . $file);
                unlink($directory . "TN" . $file);
            }
        }
        // löschen alte EvoChatSprachnachrichten / Audionachrichten
        $directory = $path . "db/" . $this->werk . "/chat/audio_chat/";
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));

        foreach ($scanned_directory as $file) {
            $checkFile = filemtime($directory . $file);
            if ($checkFile < $range) {
                unlink($directory . $file);
            }
        }

        // Löschen Eintäge in Chat, die älter als Range
        $this->deleteOldMessages($path, "", $range);
        $this->deletePersonenDatenAusProtokoll();
        echo "GabischCollector enabled";
    }
}

if (isset($_REQUEST['runtimer'])) {
    $gabish = new GabischCollector();
    $gabish->gabishCollector("../", $_REQUEST['runtimer']);
}