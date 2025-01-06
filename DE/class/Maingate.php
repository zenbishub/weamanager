<?php 
class Maingate{
public function __construct(){
     if(empty(session_id())){
          session_start();
     }
}
private function checkStatus($status, $entladeplatz=null){
     switch($status){
          case null:
          case 25:
               return "<span class='me-2 h5'><i class='ti-truck'></i></span> Warteschlange";
          break;
          case 50:
          case 501:
               return "<span class='me-2 h5'><i class='ti-truck'></i></span> Fahrzeug darf auf das Firmengelände $entladeplatz";
          break;
          case 75:
          case 751:
               return "<span class='me-2 h5'><i class='ti-truck'></i></span> Fahrzeug auf dem Firmengelände im Abladeprozess";
          break;
          case 100:
          case 1001:
               return "<span class='me-2 h5'><i class='ti-truck'></i></span> Fahrzeug auf dem Weg zu Hauptpforte";
          break;
     }
}
private function readTraffic($pfad="../"){
        
        $liteDB= new Sqlite($_SESSION['werknummer'],$pfad);
        $q = "SELECT * FROM traffic ORDER BY rfnum DESC";
        $results = $liteDB->sqliteSelect($q);
        foreach($results as $result){
            $array[]=json_decode($result['object'],true);
        }
        return $array;
}
private function inactiveTime(){
     $form = json_decode(file_get_contents("../languages/".$_SESSION['wealanguage']."/".$_SESSION['wealanguage']."_formtext.json"),true);
     return '
    <div class="row justify-content-center mt-4 p-4">
        <div class="col-12 alert-danger p-3 rounded">
            <h3>'.$form['string42'].'</h3>
        </div>
    </div>';
}
public function monitorEinfahrt($getOrder,$inaktive=""){
    if($inaktive==true){
     echo $this->inactiveTime();
     exit;
    }
     foreach($getOrder as $list){
          $cardStyle=null;
          $cardBodySytle=null;
          $cardOpacity=null;
          $entladeplatz=null;
          if(
               $list['Status']==25 ||
               $list['Status']==50 
               || $list['Status']==75 && !empty($list['Prozessname'])
               ){

               if($list['Status']==25 || $list['Status']==null){
                    $cardOpacity="opacity-50";
                    $cardStyle="";
               }
               if($list['Status']==50){
                    $cardStyle="alert-warning";
               }
               if($list['Status']==75){
                    $cardStyle="alert-warning";
               }
               if($list['Status']==100){
                    $cardStyle="alert-success";
               }
               if($list['Status']==75 && $list['Prozessname']=="Werksverkehr"){
                    $cardStyle="alert-success";
               }
               if(!empty($list['Platz'])){
                    $entladeplatz="zu <span class='fs-4'>".$list['Platz']."</span>";
               }
               if($list['Prozessname']=="Werksverkehr"){
                  $list['Nummer']  = $list['Prozessname'];
               }
               if($list['Drive-In']==1){
               $driveIn= '<div class="p-2 bg-success"></div>';
          }
          echo '<div class="card mb-1 '.$cardOpacity.' shadow">
          <div class="card-header '.$cardStyle.' h3">';
          echo '<div class="row justify-content-center">
          <div class="col-6 p-0">';
          echo $list['Nummer'];
          echo '</div>';
          echo '<div class="col-5 p-0 pe-1 text-right h2">';
          echo $list['Firma'];
          echo '</div>';
          echo '<div class="col-1 p-0 text-end">
          <img src="assets/img/flage'.$list['Sprache'].'.JPG" class="img-thumbnail p-1" style="width:60%">
          </div>';
          echo '</div>';
          echo '</div>';
          echo '<div class="card-body p-1">';
          echo '
          <div class="row">
               <div class="col-11 p-0">
               <table>
               <tr>
                    <td class="h6">'.$this->checkStatus($list['Status'], $entladeplatz);
                    if($list['Zollgut']=="JA" || $list['Zollgut']=="PASSIERT"){
                         echo '<span class="badge badge-danger fs-3 mt-4 ms-1">Achtung! ZOLLGUT on Board';
                    }
                    echo '</td>
               </tr>
               </table>
               </div>';
               echo '<div class="col-1 p-0 h3 text-center"><span class="badge fs-2 rounded-3 bg-info">'.$list['rfnum'].'</span></div>';
               echo '</div>';
          echo '</div>';
          echo $driveIn;
          echo '</div>';
        }
     }
} 
public function monitorAusfahrt($getOrder, $inaktive=""){

     if($inaktive==true){
     echo $this->inactiveTime();
     exit;
     }
     if(empty($getOrder)){
          echo "";
          exit;
     }
     krsort($getOrder);
     foreach($getOrder as $list){
          if($list['Status']==100 || $list['Status']==1001){
               $protokoll = json_decode($list['Protokoll_WA'],true);
          $cardStyle=null;
               $newValue = [];
               foreach($protokoll as $key => $value){
                    if(substr($key,0,5)!="check"){
                         $newValue[]=$value;
                    }
               }
               $newCheck = [];
               foreach($protokoll as $key => $value){
                    if(substr($key,0,5)=="check"){
                         $newCheck[]=$value;
                    }
               }
               $array = [];
               $finding=null;
               for($i=0; $i<count($newCheck);$i++){
                    if($newCheck[$i]=="nein"){
                    $array[] = [$newValue[$i]=>$newCheck[$i]];
                    }
               }
               $cardStyle="alert-success";
          if(!empty($array)){
               $cardStyle="bg-danger text-white";
               foreach($array as $issues){
                    foreach($issues as $issue=>$val){
                         $finding .=  '<tr>
                         <td class="h6 pt-2 ps-4">- <span class="me-1"><i class="ti-alert"></i></span> '.$issue.'</td>
                    </tr>';
                    }
               }
          }
          if($list['Prozessname']=="Werksverkehr"){
                  $list['Nummer']  = $list['Prozessname'];
               }
          echo '<div class="card mb-1 shadow">
          <div class="card-header '.$cardStyle.' h3">';
          echo '<div class="row justify-content-center">
          <div class="col-6 p-0">';
          echo $list['Nummer'];
          echo '</div>';
          echo '<div class="col-5 p-0 pe-1 text-right h2">';
          echo $list['Firma'];
          echo '</div>';
          echo '<div class="col-1 p-0 text-end">
          <img src="assets/img/flage'.$list['Sprache'].'.JPG" class="img-thumbnail p-1" style="width:60%">
          </div>';
          echo '</div>';
          echo '</div>';
          echo '<div class="card-body p-1">';
          echo '
          <div class="row">
               <div class="col-11 p-0">
                    <table>
                        
                         <tr>
                              <td class="h6">'.$this->checkStatus($list['Status']).'</td>
                         </tr>
                         '.$finding.'
                         <tr><td class="h4">'.$list['Abfertigung'].'</td></tr>
                    </table>
               </div>';
          echo '<div class="col-1 p-0 h3 text-center"><span class="badge fs-2 rounded-3 bg-info">'.$list['rfnum'].'</span></div>';
          echo '</div>
          </div>
          </div>';
          }
     }
}
public function scannerInfo($pfad=""){
     $liteDB= new Sqlite($_SESSION['werknummer'],$pfad);
        $q = "SELECT * FROM scanner ORDER BY id";
        $results = $liteDB->sqliteSelect($q);
        return $results;
    
     //return json_decode(file_get_contents($pfad."db/".$_SESSION['werknummer']."/scanner.json"),true);
}
public function scannerStatus($scannerID, $pfad=""){
     $rfnum = "";
     $scanners = json_decode(file_get_contents($pfad."db/".$_SESSION['werknummer']."/scannerstatus.json"),true);
     $arrays = $this->readTraffic($pfad);
     if(empty($arrays)){
          return [];
     }
     foreach($scanners as $scanner){
             if("Nr.: ".$scannerID==$scanner['scanner']){
               $rfnum = $scanner['rfnum'];
             }  
          }
     foreach($arrays as $array){
             if($rfnum==$array['rfnum'] && $array['Status']!=120){
               return [$array['rfnum'], $array['Anmeldung'], $array['Firma']];
             }  
     }
}
public function checkScannerOnline(){
     extract($_REQUEST);
     $host = "53.16.".$checkIP;
     exec("ping -n 3 " . $host, $output, $result);
     if($result==0){
          echo "<span class='badge badge-success pe-1 ps-1'>online</span>";
          exit;
     }
     echo "<span class='badge badge-danger pe-1 ps-1'>offline</span>";
}
public function callOnceByClick(){
     $rfnum = $_REQUEST['callrfnum'];
     $liteDB= new Sqlite($_SESSION['werknummer']);
     $q="SELECT object FROM traffic WHERE rfnum=$rfnum";
     $result = $liteDB->sqliteSelect($q);
     $toArray = json_decode($result[0]['object'],true);
     $toArray['alarm']="callByClick";
     $toJson = json_encode($toArray);

     $q="UPDATE traffic SET object='$toJson' WHERE rfnum=$rfnum";
     echo $liteDB->sqliteQuery($q);

}
public function modal($id, $size){
     echo '<div class="modal fade" id="'.$id.'" tabindex="-1" aria-labelledby="Label" aria-hidden="true">
        <div class="modal-dialog '.$size.'">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body overflow-auto" id="body-'.$id.'"></div>
            </div>
        </div>
    </div>';
}
}