<?php

class Sounds
{
    private $alarm;
    private $path;
    public $abt = '';


    public function __construct($pathToSound = "", $playsound = "")
    {
        $this->alarm = $playsound;
        $this->path = $pathToSound;
        if (empty(session_id())) {
            session_start();
        }

        if (isset($_SESSION['anteilung'])) {
            $this->abt = $_SESSION['anteilung'];
        }
    }
    private function getAlarm($id = "sound-file-reminding", $loop = "loop")
    {
        return '<audio src="' . $this->path . $this->alarm . '" id="' . $id . '" ' . $loop . '></audio>';
    }
    public function setAlarm($file)
    {
        $this->alarm = $file;
    }
    public function addAudio($id = "sound-file-reminding", $loop = "loop")
    {
        return $this->getAlarm($id, $loop);
    }
    private function reminderAlarm($text)
    {
        return '<div class="modal fade" id="reminderModal" data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                ' . $text . '
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary text-light"  data-bs-dismiss="modal">Sound ausschalten</button>
                </div>
                </div>
            </div>
            </div>';
    }
    private function reminderAlarmWeiterleitung($text)
    {
        return '<div class="modal fade" id="reminderModalweiterleitung" data-bs-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdrop" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                ' . $text . '
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary text-light"  data-bs-dismiss="modal">Sound ausschalten</button>
                </div>
                </div>
            </div>
            </div>';
    }
    public function conditionsListener($path = "")
    {
        $db = new Controller();
        $arrays = $db->readTrafficPublic($path);
        $count = [];
        foreach ($arrays as $key => $array) {
            if ($array['Ladung'] == "Versand Werk 5" && !empty($array['LegitimationConfirm']) && $array['Status'] < 50 && $_SESSION['abteilung'] == 602) {
                $count[] = $array['rfnum'];
            }
        }
        foreach ($arrays as $key => $array) {
            if ($array['Ladung'] == "Transport für Werk 9" && !empty($array['LegitimationConfirm']) && $array['Status'] < 50 && $_SESSION['INUMMER'] == 3) {
                $count[] = $array['rfnum'];
            }
        }
        if (count($count) > 0) {
            echo $this->getAlarm();
            echo $this->reminderAlarm("<h2>Achtung!</h2><br><h3>Ein LKW für Versand hat sich angemeldet. Bitte einsteuern!</h3>");
        }
        exit;
    }
    public function conditionsListenerWeiterleitung($path = "")
    {
        $db = new Controller();
        $arrays = $db->readTrafficPublic($path);
        $weitergeleitet = [];
        $weWerk9 = [
            "Trailerplatz Werk 9",
            "Leitplanke Werk 9",
            "Milkrunhalle"
        ];
        foreach ($arrays as $key => $array) {
            if (in_array($array['Platz'], $weWerk9) && !empty($array['Weiterleitung']) && $array['Status'] > 50 && $array['Status'] < 100 && $_SESSION['abteilung'] == 402) {
                $weitergeleitet[] = $array['rfnum'];
                $platzWerk9 = $array['Platz'];
            }
        }
        if (count($weitergeleitet) > 0) {
            echo $this->getAlarm();
            echo $this->reminderAlarmWeiterleitung("<h2>Achtung!</h2><br><h3>Ein LKW für $platzWerk9 weitergeleitet worden!</h3>");
        }
    }
}