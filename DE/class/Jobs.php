<?php

class Jobs{

    private $werk;
    private $db;
    private $sqlite;
    private $table;

    public function __construct($database, $sqliteDB,$table="current_fluss_backup", $werknummer="730"){
        $this->db = $database; 
        $this->werk=$werknummer;
        $this->table = $table;
        $this->sqlite = $sqliteDB;
    }
    private function updateTraffic($rfnum,$data){
        if(!empty($rfnum)){
            $entry=json_encode($data);
            $liteDB = $this->sqlite;
            $q="UPDATE traffic SET object='".$entry."' WHERE rfnum=$rfnum";
            $liteDB->sqliteQuery($q);
        }
    }
    public function statusChangeByIncommingTime(){
        extract($_REQUEST);
        $liteDB = $this->sqlite;
        $q="SELECT * FROM traffic";
        $results = $liteDB->sqliteSelect($q);
        echo "<ul>";
        foreach($results as $result){
            $readJsonAsArray= json_decode($result['object'],true);
            if(!empty($readJsonAsArray["customized_time"]) && date("H:i")==$readJsonAsArray["customized_time"]){
                unset($readJsonAsArray["customized_time"]);
                $readJsonAsArray["Status"]=50;
                echo $readJsonAsArray["Nummer"].", Status geändert auf ".$readJsonAsArray["Status"];
                $this->updateTraffic($result['rfnum'],$readJsonAsArray);
            }
            echo "</li>";
        }
        echo "</ul>";
    }
    public function orderingPaketDienst(){

        $liteDB = $this->sqlite;
        $q="SELECT * FROM traffic";
        $results = $liteDB->sqliteSelect($q);
        foreach($results as $result){
            $readJsonAsArray= json_decode($result['object'],true);
           if($readJsonAsArray['Ladung']=="Paketdienst" && $readJsonAsArray['Status']<50){
                $q="UPDATE traffic SET ordering=0 WHERE rfnum=".$readJsonAsArray['rfnum']."";
                $liteDB->sqliteQuery($q);
           }
        }
    }
    // Automatisches Abmelden wenn es zu lange in Dashboard stehen bleibt 
    public function clearNotQiuttierte(){
        $liteDB = $this->sqlite;
        $q="SELECT * FROM traffic";
        $results = $liteDB->sqliteSelect($q);
        foreach($results as $result){
            $readJsonAsArray= json_decode($result['object'],true);
           if(!empty($readJsonAsArray['Abfertigung'])){
            $expl = explode(", ",$readJsonAsArray['Abfertigung']);
                if(!empty($expl[1])){
                    $e = explode(":",$expl[1]);
                    $abfertigung = mktime($e[0],$e[1],0,date("m"),date("d"),date("Y"));
                    $range = time()-1800;//30 Minuten 
                        if($abfertigung<$range && $readJsonAsArray['Status']==100){
                            $readJsonAsArray['Status'] = 120;
                            $readJsonAsArray['Autoquitt']= date("d.m.y, H:i",time());
                            $readJsonAsArray['gone']= time();
                            $toJson = json_encode($readJsonAsArray);
                            $q="UPDATE traffic SET object='$toJson' WHERE rfnum=".$result['rfnum']."";
                            $liteDB->sqliteQuery($q);
                            $this->setToArchiv(json_encode($readJsonAsArray));
                        }
                }
           }
           $wvRange = time()-3600;// 30 Min.
           if($readJsonAsArray['Prozessname']=="Werksverkehr" && $readJsonAsArray['timestamp']<$wvRange && $readJsonAsArray['Status']!=120){
                $readJsonAsArray['Status'] = 120;
                $readJsonAsArray['Abfertigung']= date("d.m.y, H:i",time());
                $readJsonAsArray['gone']= time();
                $toJson = json_encode($readJsonAsArray);
                $q="UPDATE traffic SET object='$toJson' WHERE rfnum=".$result['rfnum']."";
                $liteDB->sqliteQuery($q);
                $this->setToArchiv(json_encode($readJsonAsArray));
           }
        }
    }
    private function setToArchiv($entry){
        $database = "wareneingang";
        $werknummer = $this->werk;
        $q="INSERT INTO ".$database.".dbo.werk_$werknummer(object_runleaf,tstamp)VALUES('$entry',".time().")";
        $this->db->query($q);
    }
    public function avisierungFrachtenAusSAP(){
        $getScript = file_get_contents("../db/".$this->werk."/extern/GUI/quelle.txt");
        $startRange = date("d.m.Y",time()-2592000); // 30 Tage
        $current = date("d.m.Y");
        $tmp = str_replace("%von%",$startRange,$getScript);
        $script = str_replace("%bis%",$current,$tmp);
        file_put_contents("../db/".$this->werk."/extern/GUI/avisierungSAPfrachten.vbs",$script);
            require_once 'AvisierungSAP.php';
            $avis = new AvisierungSAP($this->werk,"../");
            $res = $avis->insertTableInformations("sapsource");
            if($res==1){
                echo "Avisierung für in SAP erstellte Transportauträge hochgeladen";
            }
           
    }
    public function avisierungWerk9(){
        $database = "wareneingang";
        $q="SELECT object_frz FROM ".$database.".dbo.tracker WHERE kennzeichen='hegelmann'";
        $results = $this->db->select($q);
       $toArray = json_decode($results[0]['object_frz'],true);
        foreach($toArray as $kenz => $result){
            foreach($result as $array){
                if($array['transport_for']=="WERK 9" || $array['transport_for']=="direct"){
                $arrayHegelmann[] = $kenz;
                }
            }
        }
        $liteDB = $this->sqlite;
        $q="SELECT * FROM traffic";
        $results = $liteDB->sqliteSelect($q);
        foreach($results as $result){
            $readJsonAsArray= json_decode($result['object'],true);
            if(!empty($readJsonAsArray['Nummer'])){
                if(in_array($readJsonAsArray['Nummer'],$arrayHegelmann)){
                    $readJsonAsArray['Ladung']="Transport für Werk 9";
                    $toJson = json_encode($readJsonAsArray);
                    $q="UPDATE traffic SET object='$toJson' WHERE rfnum=".$result['rfnum']."";
                    $liteDB->sqliteQuery($q);
                }
            }
        }
    }
}
require_once 'connect.php';
require_once 'Sqlite.php';

$db     = new connect();
$sqlite = new Sqlite("730");
$o      = new Jobs($db,$sqlite);
if(isset($_REQUEST['change_status']) && $_REQUEST['change_status']=="on"){
    $o->statusChangeByIncommingTime(); 
    $o->avisierungFrachtenAusSAP();
    $o->orderingPaketDienst();
    $o->clearNotQiuttierte();
    $o->avisierungWerk9();
}