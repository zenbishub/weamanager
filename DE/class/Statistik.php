<?php


class Statistik
{

    private $db;
    private $WN;
    private $timerange = 604800; // 7 Tage

    public function __construct($path = "")
    {
        if (empty(session_id())) {
            session_start();
        }
        require_once 'connect.php';
        $database = new connect();
        $this->db = $database;
        $this->WN = $_SESSION['werknummer'];
        error_reporting(0);
    }
    private function datasets($range = "")
    {
        if (!empty($range)) {
            $time = time() - $range;
            $where = "WHERE tstamp>$time";
        }
        $q = "SELECT id,object_runleaf FROM  werk_" . $this->WN . " $where ORDER BY id ASC";
        return $this->db->select($q);
    }
    public function monthInDatabase()
    {
        $data = $this->datasets();
        foreach ($data as $arrays) {
            $toArray = json_decode($arrays['object_runleaf'], true);
            $expl = explode(",", $toArray['Anmeldung']);
            $e = explode(".", $expl[0]);
            $findMonth = $e[1] . ".20" . $e[2];
            $arr[$findMonth] = $findMonth;
        }
        return $arr;
    }
    public function collectSpeditionData()
    {
        if (isset($_REQUEST['ajaxGetChartData'])) {
            extract($_REQUEST);
            $currentMon = $month;
            $data = $this->datasets();
            if (empty($data)) {
                echo "keine Daten";
                exit;
            }
            foreach ($data as $key => $objects) {
                $decode = json_decode($objects['object_runleaf'], true);
                //echo $decode['Anmeldung'];
                $expl = explode(",", $decode['Anmeldung']);
                $e = explode(".", $expl[0]);
                $findMonth = $e[1] . ".20" . $e[2];
                if ($findMonth == $currentMon) {
                    $arr[strtoupper(trim($decode['Firma']))][] = $decode['rfnum'];
                }
            }
            if (empty($arr)) {
                echo json_encode([]);
                exit;
            }
            foreach ($arr as $key => $value) {
                $array[$key] = count($value);
            }

            arsort($array);
            foreach ($array as $key => $value) {
                $return["data"]["spedition"][] = $key;
                $return["data"]["counts"][] = $value;
                $total[] = $value;
            }
            $return["data"]["Monat"][] = $currentMon . " Gesamt: " . array_sum($total);
            echo json_encode($return);
        }
    }
    public function collectZollData()
    {
        if (isset($_REQUEST['ajaxGetZollChartData'])) {
            extract($_REQUEST);
            $currentMon = $month;
            $data = $this->datasets();
            if (empty($data)) {
                echo "keine Daten";
                exit;
            }
            foreach ($data as $key => $objects) {
                $decode = json_decode($objects['object_runleaf'], true);
                $expl = explode(",", $decode['Anmeldung']);
                $e = explode(".", $expl[0]);
                $findMonth = $e[1] . ".20" . $e[2];
                if ($findMonth == $currentMon) {
                    if ($decode['Zollgut'] == "JA" || $decode['Zollgut'] == "PASSIERT") {
                        if (!empty($decode['Zollgut'])) {
                            $arr[trim(strtoupper($decode['Firma']))][] = $decode['rfnum'];
                        }
                    }
                }
            }

            if (empty($arr)) {
                echo json_encode([]);
                exit;
            }
            foreach ($arr as $key => $value) {
                $array[$key] = count($value);
            }
            arsort($array);

            foreach ($array as $key => $value) {
                $return["data"]["spedition"][] = $key;
                $return["data"]["counts"][] = $value;
                $total[] = $value;
            }
            $return["data"]["Monat"][] = $currentMon . " Gesamt: " . array_sum($total);
            echo json_encode($return);
        }
    }
    private function toTstamp($tString)
    {
        $expl = explode(", ", $tString);
        $datum = $expl[0];
        $time = $expl[1];
        $eDatum = explode(".", $datum);
        $eTime = explode(":", $time);

        return mktime($eTime[0], $eTime[1], 0, $eDatum[1], $eDatum[0], "20" . $eDatum[2]);
    }
    public function calculations()
    {
        $datasets = $this->datasets();

        foreach ($datasets as $key => $objects) {
            $decode = json_decode($objects['object_runleaf'], true);
            if (strtolower($decode['Firma']) == "hegelmann" && !empty($decode['Abfertigung'])) {
                $anmeldung = $this->toTstamp($decode['Anmeldung']);
                $abfertigung = $this->toTstamp($decode['Abfertigung']);
                $delta = ($abfertigung - $anmeldung) / 60;
                $firma[strtolower($decode['Firma'])][$delta] = $decode['Anmeldung'];
            }
        }
        foreach ($firma as $name => $minuten) {
            echo $name;
            echo "<hr>";
            ksort($minuten);
            foreach ($minuten as $key => $value) {
                echo $key . " Min. / " . $value;
                echo "<br>";
            }
        }
    }
    public function dataForPerformance()
    {
        extract($_REQUEST);
        $currentMon = $month;
        $data = $this->datasets(2851200);
        foreach ($data as $arrays) {
            $toArray = json_decode($arrays['object_runleaf'], true);
            $filter = substr($toArray['Anmeldung'], 0, -3);
            $arr[str_replace(" ", "", $filter)][] = 1;
        }
        foreach ($arr as $datum => $countArr) {
            $expl = explode(",", $datum);
            $e = explode(".", $expl[0]);
            $findMonth = $e[1] . ".20" . $e[2];
            if ($findMonth == $currentMon) {
                $array[$findMonth][$expl[0]][$expl[1]] = array_sum($countArr);
            }
        }
        foreach ($array as $monat => $mdays) {
            foreach ($mdays as $days) {
                foreach ($days as $stunde => $day) {
                    $monatData[$monat][$stunde][] = $day;
                }
            }
        }
        foreach ($monatData as $m => $values) {
            $dataSet["Monat"] = $m;
            ksort($values);
            foreach ($values as $stunde => $value) {
                $dataSet["Stunde"][] = $stunde . ":00";
                $dataSet["Counts"][] = array_sum($value);
            }
        }
        echo json_encode($dataSet);
    }
    public function getDataForDailyChart()
    {
        extract($_REQUEST);
        $currentDay = $dayData;
        $data = $this->datasets();
        foreach ($data as $key => $objects) {
            $decode = json_decode($objects['object_runleaf'], true);
            $expl = explode(",", $decode['Anmeldung']);
            $findMonth = trim($expl[0]);
            if ($findMonth == $currentDay) {
                $e = explode(":", trim($expl[1]));
                $arr[$e[0]][] = $decode['rfnum'];
            }
        }

        if (empty($arr)) {
            echo json_encode([]);
            exit;
        }
        foreach ($arr as $key => $value) {
            $array[$key] = count($value);
        }
        ksort($array);
        // print_R($array);

        foreach ($array as $key => $value) {
            $return["data"]["Stunden"][] = $key . ":00";
            $return["data"]["counts"][] = $value;
            $total[] = $value;
        }
        $return["data"]["Day"][] = $currentDay . " Gesamt: " . array_sum($total);
        echo json_encode($return);
    }
}

$o = new Statistik();
if (isset($_REQUEST['ajaxGetZollChartData']) || isset($_REQUEST['ajaxGetChartData'])) {
    $o->collectSpeditionData();
    $o->collectZollData();
    if (isset($_REQUEST['calc'])) {
        $o->calculations();
    }
}
if (isset($_REQUEST['getChartDataPerformance'])) {
    $o->dataForPerformance();
}
if (isset($_REQUEST['getDataForDailyChart'])) {
    $o->getDataForDailyChart();
}