<?php

class Timeline
{
    private $liteDB;
    public function __construct($path = "")
    {
        if (empty(session_id())) {
            session_start();
        }
        require_once $path . 'Sqlite.php';
        $this->liteDB = new Sqlite($_SESSION['werknummer']);
    }
    private function entriesData($dayData = "", $anotherDay = false)
    {
        $q = "SELECT rfnum, object FROM traffic ORDER BY rfnum DESC";
        $results = $this->liteDB->sqliteSelect($q);
        if ($anotherDay == "true") {
            require_once 'Lieferant.php';
            $lieferant = new Lieferant();
            $results = $lieferant->getDataFromArchiv($dayData);
            krsort($results);
        }
        foreach ($results as $result) {
            $toArray = json_decode($result['object'], true);
            $anmeldungStamp = $toArray['timestamp'];
            $goneStamp = $toArray['gone'];
            $quitt = "Pforte";
            if (!empty($toArray['Autoquitt'])) {
                $quitt = "Autoquitt";
            }

            $arr[$toArray['rfnum']] = [
                "Anmeldung" => $anmeldungStamp,
                "Gone" => $goneStamp,
                "Firma" => $toArray['Firma'],
                "Platz" => $toArray['Platz'],
                "Quittierung" => $quitt
            ];
        }
        return $arr;
    }
    public function timelineTable($dayData = "", $anotherDay = "")
    {
        return $this->entriesData($dayData, $anotherDay);
    }
    public function timeLineData()
    {
        $anotherDay = null;
        $dayData = null;
        $dayStart = mktime(5, 0, 0, date("m"), date("d"), date("Y"));
        $dayEnd = mktime(20, 0, 0, date("m"), date("d"), date("Y"));
        if (!empty($_REQUEST['anotherDay'])) {
            $anotherDay = $_REQUEST['anotherDay'];
            $dayData = $_REQUEST['dayData'];
            $expl = explode(".", $dayData);
            $dayStart = mktime(5, 0, 0, $expl[1], $expl[0], date("Y"));
            $dayEnd = mktime(20, 0, 0, $expl[1], $expl[0], date("Y"));
        }
        $arrays = $this->timelineTable($dayData, $anotherDay);

        foreach ($arrays as $key => $array) {
            $data[] = $key . ";" . $dayStart . ";" . $dayEnd . ";" . $array['Anmeldung'] . ";" . $array['Gone'];
        }
        return json_encode($data);
    }
}

if (isset($_REQUEST['getTimelineData'])) {
    $o = new Timeline();
    echo $o->timeLineData();
}