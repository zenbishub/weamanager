<?php

class Evochat
{
    private $dbpath;
    private $chatPath;
    public function __construct($werknummer, $path = "")
    {
        $this->dbpath = $path . "db/" . $werknummer . "/chat";
        $this->chatPath = "https://" . $_SERVER['HTTP_HOST'] . "/weamanager/de/db/$werknummer/chat/";
        $limit = time() - 86400;
        $this->chatGabishCollector($limit, $path);
        error_reporting(0);
    }
    public function createStucture()
    {
        if (!is_dir($this->dbpath)) {
            mkdir($this->dbpath);
        }
        if (!file_exists($this->dbpath . "/evochat.json")) {
            file_put_contents($this->dbpath . "/evochat.json", "");
        }
    }
    public function startChat($path = "")
    {
        if (isset($_REQUEST['send_to_evochat'])) {
            if (!empty($_FILES)) {
                $imagePath = $this->dbpath . "/image_chat/";
                require_once 'class_image_edit.php';
                $image = new imageEdit();
                $filename = "image_" . time() . ".jpg";
                if (move_uploaded_file($_FILES['chat_attachment']['tmp_name'], $imagePath . $filename)) {
                    $image->resize_image($imagePath . $filename, 900, 680);
                    $image->machAvatar($imagePath, $imagePath, $filename, 100);
                    $_POST['attachment'] = $filename;
                }
            }
            if (!empty($_POST['addaudio'])) {
                $addaudio = $_POST['addaudio'];
                $audioPath = $this->dbpath . "/audio_chat/";
                $filename = "audio_" . time() . ".mp3";
                file_put_contents($audioPath . $filename, base64_decode($addaudio));
                $_POST['audio'] = $filename;
            }
            $merged = [];
            $readDB = file_get_contents($this->dbpath . "/evochat.json");
            unset($_POST['addaudio']);
            $array[] = $_POST;
            if (empty($readDB)) {
                $merged = $array;
            }
            if (!empty($readDB)) {
                $readJsonAsArray = json_decode($readDB, true);
                $merged = array_merge($array, $readJsonAsArray);
            }
            $entry = json_encode($merged, JSON_PRETTY_PRINT);
            file_put_contents($this->dbpath . "/evochat.json", $entry);
            exit;
        }
    }
    public function evochatLastMessageBox($key, $bmi_nummer = "")
    {
        echo '<div id="getmessage" data="' . $bmi_nummer . '&' . $_SESSION['werknummer'] . '" title="' . $_SESSION['werknummer'] . '" class="shadow border d-none">
        <span id="close-getmessage" class="float-end pointer">
            <i class="ti-close"></i>
        </span>
            <div id="getmessage-text" data-index="' . $key . '" class="row mt-4"></div>
        </div>';
    }
    private function messageCart($userchat)
    {

        if (empty($userchat)) {
            echo "<div class='row p-2 small'><div class='col-12 text-center small'>keine Nachrichten</div></div>";
            exit;
        }
        foreach ($userchat as $posts) {
            $tab = "";
            $attachment = "";
            $audio = "";
            $justifyColor = "alert-warning";
            if ($posts['Absender'] == $_SESSION['weamanageruser'] || $posts['Empfaenger'] == "Zentrale") {
                $tab = "justify-content-end";
                $justifyColor = "alert-success";
            }
            if ($posts['Empfaenger'] != $_SESSION['weamanageruser']) {
                $gelesen = '<div class="small"><span class="float-end small font-italic">zugestellt</span></div>';
                if (!empty($posts['gelesen'])) {
                    $gelesen = '<div class="small"><span class="float-end small font-italic">' . $posts['gelesen'] . '</span></div>';
                }
            }
            if (!empty($posts['attachment'])) {
                $attachment = "<img class='pointer img-thumbnail d-block open-attachment' src='" . $this->chatPath . "image_chat/TN" . $posts['attachment'] . "' alt='" . $this->chatPath . "image_chat/" . $posts['attachment'] . "'>";
            }
            if (!empty($posts['audio'])) {
                $audio = "<audio controls><source src='" . $this->chatPath . "audio_chat/" . $posts['audio'] . "' type='audio/mpeg'></audio>";
            }
            echo '<div class="row ' . $tab . '">
                <div class="col-md-10">
                    <div class="card mb-2 bg-transparent border-0 shadow-none">
                        <div class="card-header p-1 border-0 bg-transparent">
                            <div class="row small">
                                <div class="col-6 p-0 small">
                                ' . date("H:i", $posts['send_to_evochat']) . '
                                </div>
                                <div class="col-6 p-0 small text-right">
                                ' . $posts['Absender'] . '
                                </div>
                            </div>
                        </div>
                <div class="card-body ' . $justifyColor . ' border rounded shadow p-1">
                ' . $posts['message-text'] . '
                ' . $attachment . '
                ' . $audio . '
                ' . $gelesen . '
                </div>
            </div></div>
            </div>';
        }
    }
    public function showChat($user, $empfaenger, $absender, $path = "")
    {
        $readDB = file_get_contents($path . $this->dbpath . "/evochat.json");
        if (!empty($readDB)) {
            $readJsonAsArray = json_decode($readDB, true);
            foreach ($readJsonAsArray as $key => $array) {
                if (($array['Absender'] == $user && $array['Empfaenger'] == $absender)
                    || ($array['Empfaenger'] == $user && $array['Absender'] == $absender)
                    || $array['Target'] == "SF-JIT"
                ) {
                    $userchat[] = $array;
                }
            }
            if (!empty($userchat)) {
                $toJson = json_encode($readJsonAsArray, JSON_PRETTY_PRINT);
                file_put_contents($path . $this->dbpath . "/evochat.json", $toJson);
                krsort($userchat);
                echo $this->messageCart($userchat, $empfaenger);
            }
        }
    }
    private function chatGabishCollector($limit, $path = "")
    {
        $readDB = file_get_contents($this->dbpath . "/evochat.json");
        if (!empty($readDB)) {
            $readJsonAsArray = json_decode($readDB, true);
            foreach ($readJsonAsArray as $key => $array) {
                if ($array['send_to_evochat'] < $limit) {
                    $image = $readJsonAsArray[$key]['attachment'];
                    $audio = $readJsonAsArray[$key]['audio'];
                    unlink($this->dbpath . "/image_chat/$image");
                    unlink($this->dbpath . "/image_chat/TN$image");
                    unlink($this->dbpath . "/audio_chat/$audio");
                    unset($readJsonAsArray[$key]);
                }
            }
            $entry = json_encode($readJsonAsArray, JSON_PRETTY_PRINT);
            file_put_contents($this->dbpath . "/evochat.json", $entry);
        }
    }
    public function evoChatLastShowMessage($path = "")
    {
        if (isset($_REQUEST['evoChatLastShowMessage'])) {
            extract($_REQUEST);
            $werknum = $_REQUEST['werknummer'];
            $empfaenger = $_REQUEST['empfaenger'];
            $readDB = file_get_contents($path . "db/" . $werknum . "/chat/evochat.json");
            switch ($evoChatLastShowMessage) {
                case "Zentrale":
                    if (!empty($readDB)) {
                        $readJsonAsArray = json_decode($readDB, true);
                        foreach ($readJsonAsArray as $key => $array) {
                            if ($array['Empfaenger'] == "Zentrale" && empty($array['gelesen'])) {
                                $return[$key] = $array;
                            }
                            if ($array['Target'] == "Zentrale" && empty($array['gelesen'])) {
                                $return[$key] = $array;
                            }
                        }
                        if (!empty($return)) {
                            foreach ($return as $key => $messages) {
                                if ($messages['gelesen'] != "gelesen") {

                                    $attachment = "";
                                    $audio = "";
                                    if (!empty($messages['attachment'])) {
                                        $attachment = "<img class='pointer img-thumbnail d-block attachment-mini' src='" . $this->dbpath . "/image_chat/TN" . $messages['attachment'] . "'>";
                                    }
                                    if (!empty($messages['audio'])) {
                                        $audio = "<audio controls><source src='" . $this->dbpath . "/audio_chat/" . $messages['audio'] . "' type='audio/mpeg'></audio>";
                                    }
                                    echo '<div class="col-12 border alert-success rounded mb-2 message-item pointer" data="' . $messages['Absender'] . '&' . $messages['Empfaenger'] . '" title="' . $messages['Absender'] . '" alt="' . $empfaenger . '" data-index="' . $key . '">
                                        <span class="font-italic small">' . date("d.m H:i", $messages['send_to_evochat']) . '</span>
                                        <span class="d-block mb-1 font-weight-bold">' . $messages['Absender'] . '</span>
                                        <p class="font-semilarge">' . $messages['message-text'] . ' ' . $attachment . ' ' . $audio . '</p>
                                        </div>';
                                }
                            }
                        }
                    }
                    break;
                default:

                    if (!empty($readDB)) {
                        $readJsonAsArray = json_decode($readDB, true);
                        foreach ($readJsonAsArray as $key => $array) {
                            if ($array['Empfaenger'] == $empfaenger && empty($array['gelesen']) || $array['Absender'] == "SITZFERTIGUNG" && $array['Target'] == "SF-JIT") {
                                $return[$key] = $array;
                            }
                        }

                        if (!empty($return)) {
                            foreach ($return as $key => $messages) {
                                if ($messages['gelesen'] != "gelesen") {

                                    $attachment = "";
                                    $audio = "";
                                    if (!empty($messages['attachment'])) {
                                        $attachment = "<img class='pointer img-thumbnail d-block attachment-mini' src='" . $this->dbpath . "/image_chat/TN" . $messages['attachment'] . "'>";
                                    }
                                    if (!empty($messages['audio'])) {
                                        $audio = "<audio controls><source src='" . $this->dbpath . "/audio_chat/" . $messages['audio'] . "' type='audio/mpeg'></audio>";
                                    }
                                    echo '<div class="col-12 alert-success rounded border mb-2 message-item pointer" data="' . $messages['Empfaenger'] . '&' . $messages['Absender'] . '" alt="' . $empfaenger . '" data-index="' . $key . '">
                                    <span class="font-italic small">' . date("d.m H:i", $messages['send_to_evochat']) . '</span>
                                    <span class="d-block mb-1 font-weight-bold">' . $messages['Absender'] . '</span>
                                    <p class="font-semilarge">' . $messages['message-text'] . ' ' . $attachment . ' ' . $audio . '</p>
                                    </div>';
                                    echo $ping;
                                }
                            }
                        }
                    }
            }
        }
    }
    public function evoChatReadedLastMessage($path)
    {
        if (isset($_REQUEST['evochatmessagereaded'])) {
            $messageID = $_REQUEST['evochatmessagereaded'];
            $readDB = file_get_contents($path . "db/" . $_SESSION['werknummer'] . "/chat/evochat.json");
            $arrays = json_decode($readDB, true);
            foreach ($arrays as $key => $array) {
                if ($key == $messageID) {
                    $array['gelesen'] = "gelesen";
                }
                $new[] = $array;
            }
            $entry = json_encode($new, JSON_PRETTY_PRINT);
            echo  file_put_contents($path . "db/" . $_SESSION['werknummer'] . "/chat/evochat.json", $entry);
        }
    }
    public function listOfEmpfaenger($path = "")
    {
        $readDB = file_get_contents($path . "db/" . $_SESSION['werknummer'] . "/bmi.json");
        switch ($_REQUEST['callChat']) {
            case "pforte":
                $return["Zentrale"] = "Wareneingang W5";
                $return["ZOLLSTELLE"] = "ZOLLSTELLE";
                $return["Versand"] = "Versand / SKD";
                echo "<div class='row'>";
                echo "<div class='col-6 p-0 form-group'>";
                echo "Nachricht senden an";
                echo "<select class='form-control text-black' id='select-empfaenger' required>";
                echo "<option value='' >Empfänger wählen</option>";
                foreach ($return as $key => $value) {
                    echo "<option value='$key' >$value</option>";
                }
                echo "</select>";
                echo "</div>";
                echo "</div>";
                break;
            case "Zentrale":
            case "zollstelle":
            default:
                $return["Zentrale"] = "Wareneingang W5";
                $return["Wareneingang Werk 9"] = "Wareneingang W9";
                $return["PFORTE"] = "PFORTE W5";
                $return["ZOLLSTELLE"] = "ZOLLSTELLE";
                $return["Versand"] = "Versand / SKD";
                $return["Lack"] = "Lack";
                $return["SITZFERTIGUNG"] = "SITZFERTIGUNG";
                if (!empty($readDB)) {
                    $readJsonAsArray = json_decode($readDB, true);
                    foreach ($readJsonAsArray as $key => $value) {
                        $return[$value['BMI-Nummer']] = $value['BMI-Nummer'] . " " . $value['BMI-Typ'] . " " . $value['Hersteller'];
                    }
                    echo "<div class='row'>";
                    echo "<div class='col-6 p-0 form-group'>";
                    echo "Nachricht senden an";
                    echo "<select class='form-control text-black' id='select-empfaenger' required>";
                    echo "<option value='' >Empfänger wählen</option>";
                    foreach ($return as $key => $value) {
                        echo "<option value='$key' >$value</option>";
                    }
                    echo "</select>";
                    echo "</div>";
                    echo "</div>";
                }
                break;
        }
    }
}