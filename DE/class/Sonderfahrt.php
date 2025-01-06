<?php 

class Sonderfahrt{
    public $dbpath;
    private $data =[];
    private $wn;

public function __construct($werknummer,$path=""){
        $this->wn = $werknummer;
        $this->dbpath=$path."db/".$werknummer;
        $this->data = [
            "rfnum"=>null,
            "Werknummer"=>$werknummer,
            "Werkname"=>null,
            "anmeldeID"=>null,
            "Firma"=>"B&B",
            "Sprache"=>null,
            "Name Fahrer"=>null,
            "Legitimation"=>null,
            "FRZTyp"=>null,
            "Nummer"=>null,
            "Ladung"=>null,
            "Gefahrgutpunkte"=>null,
            "Zollgut"=>null,
            "kennzeichnugspflichtig"=>null,
            "Lieferschein"=>null,
            "Beladen für"=>null,
            "Entladen"=>null,
            "ladung_beschreibung"=>null,
            "Anmeldung"=>date("d.m.y, H:i",time()),
            "timestamp"=>time(),
            "Platz"=>null,
            "Status"=>null,
            "Entladung"=>null,
            "Protokoll_WA"=>null,
            "Protokoll_VERS"=>null,
            "Abfertigung"=>null,
            "gone"=>null,
            "alarm"=>null,
            "WA_Buro"=>null
        ];
        error_reporting(0);
}
private function getRFID($path=""){
        $readDB = file_get_contents($path.$this->dbpath."/sonderfahrt.json");
        $id=1;
        if(!empty($readDB) && $readDB!="null"){
            $readJsonAsArray= json_decode($readDB,true);
           foreach($readJsonAsArray as $array){
                $expl = explode("-",$array['rfnum']);
                $tmp[] = $expl[1];
           }
           $id = count($tmp)+1;
        }
        return $id;
}
private function makeTimestamp($lieferdatum, $zeitfenster){
        $expl = explode(".",$lieferdatum);
        $zf = explode(":",$zeitfenster);
        return mktime($zf[0],$zf[1],0,$expl[1],$expl[0],date("Y"));
}
public function addSonderfahrt($path=""){
         if(isset($_REQUEST['add_sonderfahrt'])){
            extract($_REQUEST);
            $reutrnURI= $_REQUEST['reutrnURI'];
            unset($_POST['add_sonderfahrt']);
            unset($_POST['reutrnURI']);
            
        $readDB = file_get_contents($path."db/".$this->wn."/sonderfahrt.json");
        $rfnum = $this->getRFID($path);
            
        $arrLet = ["A","B","C","D","E","F","G"];
        $day = date("w",$this->makeTimestamp($_POST['Lieferdatum'],$_POST['Zeitfenster']))-1;
            
           $_POST["dispo_ID"] = $_SESSION['weamanageruser'];
           $_POST["sofahnum"]="SOF-$rfnum-".$arrLet[$day];
           $_POST['Lief_tstamp'] = $this->makeTimestamp($_POST['Lieferdatum'],$_POST['Zeitfenster']);
           $tstamp = $_POST['Lief_tstamp'];
           $_POST['Anmeldung']= date("d.m.y H:i",$this->makeTimestamp($_POST['Lieferdatum'],$_POST['Zeitfenster']));
           $_POST['erstellt'] = time();

        if(!empty($readDB)){
            $readJsonAsArray= json_decode($readDB,true);
            foreach($readJsonAsArray as $key=>$value){
                
                if($key=='sofahnum' && $_POST["sofahnum"]!=$value){
                    $array[$tstamp] = array_merge($this->data,$_POST);
                    $readJsonAsArray[]= $array;
                    
                    $entry=json_encode($readJsonAsArray,JSON_PRETTY_PRINT);
                    $res = file_put_contents($path.$this->dbpath."/sonderfahrt.json",$entry);
                    if(!empty($res)){
                        header("location:".$path."".$reutrnURI."?add_sof=success");
                        exit;
                    }
                }
            }
            header("location:".$path."".$reutrnURI."?add_sof=failed"); 
        }
        $readJsonAsArray[$tstamp]=array_merge($this->data,$_POST);
        $entry=json_encode($readJsonAsArray,JSON_PRETTY_PRINT);
        $res=file_put_contents($path.$this->dbpath."/sonderfahrt.json",$entry);
        if(!empty($res)){
            header("location:".$path."".$reutrnURI."?add_sof=success");
            exit;
        }
        header("location:".$path."".$reutrnURI."?add_sof=failed"); 
        exit;
    }
}
public function sonderfahrtenByDispoID($path=""){
    $readDB = file_get_contents($path.$this->dbpath."/sonderfahrt.json");
    $dispo_ID = $_SESSION['weamanageruser'];
    $arrays = json_decode($readDB,true);
    foreach($arrays as $array){
        if($array['dispo_ID']==$dispo_ID){
            $filter[] = $array; 
        }
    }
    return $filter;
}
public function sonderfahrtenBySofahNummer($sofahnummer, $path=""){
    $filter = [];
    $readDB = file_get_contents($path.$this->dbpath."/sonderfahrt.json");
    $arrays = json_decode($readDB,true);
    if(empty($arrays)){
        return [];
    }
    foreach($arrays as $array){
        if($array['sofahnum']==$sofahnummer){
            $filter[] = $array; 
        }
    }
    return $filter;
}
public function deleteSonderfahrtenBySofahNummer($sofahnummer, $path=""){
    $readDB = file_get_contents($path.$this->dbpath."/sonderfahrt.json");
    $arrays = json_decode($readDB,true);
    $timelimit = time()-86400;
    foreach($arrays as $array){
        if($array['sofahnum']!=$sofahnummer || $array['Lief_tstamp']<$timelimit){
            $filter[] = $array; 
        }
    }
    $entry=json_encode($filter,JSON_PRETTY_PRINT);
    file_put_contents($path.$this->dbpath."/sonderfahrt.json",$entry);
}
public function deleteSonderfahrtenByTimelimit($path=""){
    $readDB = file_get_contents($path.$this->dbpath."/sonderfahrt.json");
    $arrays = json_decode($readDB,true);
    $timelimit = time()-43000;
    foreach($arrays as $array){
        if($array['Lief_tstamp']>$timelimit){
            $filter[] = $array; 
        }
    }
    $entry=json_encode($filter,JSON_PRETTY_PRINT);
    file_put_contents($path.$this->dbpath."/sonderfahrt.json",$entry);
}
private function getAnmeldeID($anmeldeID, $route, $werknummer){
    
    switch($route){
        case "Lieferung_ID":
            require_once 'connect.php';
            $o = new connect();
            $q="SELECT * FROM werk_".$werknummer." WHERE anmeldeID LIKE '$anmeldeID'";
            $data = $o->select($q);
                if(empty($data[0]['anmeldeID'])){
                    echo "ID invalid";
                    exit;
                }
            $array = json_decode($data[0]['object_runleaf'],true);
            $string="";
                foreach($array as $val){
                    $string .= $val.",";
                }
                echo substr($string,0,-1);
        break;
        case "Sonderfahrt":
            $arrays = $this->sonderfahrtenBySofahNummer($anmeldeID,"../");
            
                if(empty($arrays)){
                    echo "ID invalid";
                    exit;
                }
            
                foreach($arrays as $array){
                    if($array['sofahnum'] == $anmeldeID){
                        $data = $array;
                    }
                }
                if(empty($data)){
                    echo "ID invalid";
                    exit;
                }
                if(!empty($data)){
                    $string = $data['Werknummer'].":".$data['Werkname'].":".$data['Firma'].":Sonderfahrt,";
                    echo substr($string,0,-1);
                }
        break;
    }
}
public function checkAnmeldeID($anmeldeID, $route, $werknummer){
    return $this->getAnmeldeID($anmeldeID, $route, $werknummer);
}
private function sendMail($toemail,$replyTo,$betreff,$nachricht){
    $empfaenger = $toemail;
	$header = "From: tmi.lkwmanager@daimlertruck.com" . "\r\n" .
				"Reply-To:". $replyTo . "\r\n" .
				"X-Mailer: PHP/" . phpversion()." \r\n".
				"Content-type: text/html; charset=utf-8\n";
	return mail($empfaenger,$betreff,$nachricht,$header);
}
public function sonderFahrtOngate($toemail,$replyTo, $person, $sofahnummer){
    $betreff ="Sonderfahrt hat sich an der Prorte angemeldet";
    $nachricht = "Hallo ".$person. ", <p>Die Sonderfahrt mit der Nummer ".$sofahnummer." hat sich an der Pforte angemeldet.</p>";
    $nachricht .= "<p>Automatische Benachrichtigung, bitte keine Rückantwort senden</p>";
    $this->sendMail($toemail,$replyTo,$betreff,$nachricht);
    $this->deleteSonderfahrtenBySofahNummer($sofahnummer, "../");
}
}