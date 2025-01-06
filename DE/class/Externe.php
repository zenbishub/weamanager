<?php

class Externe{

    private $dbpath;
    private $tb="tracker";
    private $connect;
    private $path;
    public function __construct($werknummer,$path=""){
        require_once 'connect.php';
        $this->connect = new connect();
        $this->dbpath=$path."db/".$werknummer."/";
        $this->path = $path;
        error_reporting(0);
    }
    private function referenceTable($pfad=""){
        $file = $pfad."ekol_deliver_information.xlsx";
        require $this->path.'vendor/autoload.php';
        $spreadsheet = PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $worksheet =$spreadsheet->getSheet(0);
        $lastRow = $worksheet->getHighestRow()-1;
        $colomncount = $worksheet->getHighestDataColumn();
        $colomncount_number = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($colomncount)-1;
        $fileTime = date("d.m.Y, H:i",filemtime($file));
        $table .= "<span class='small'>Datei von: ".$fileTime."</span><table align='center' class='table table-striped bg-white'>";
            for($row=5;$row<=$lastRow;$row++){
                $colspan=null;
                if($row==3){
                    $colspan= "colspan='4'";
                }
                $secondary=null;
                $boldCell=null;
                    if($row==5){
                        $secondary="bg-secondary ";
                        $boldCell = "font-weight-bold text-light p-2";
                    }
                    if($row>5){
                        $trhover="tr-hover";
                    }

                $table .= "<tr class='$secondary $trhover'>";
                $table .=  "<td class='pl-1 pr-2 click-hover'>";
                    if($row-5>0){$table .= "<span class='pointer'>".($row-5)."</span>";}
                echo "</td>";
                for($col=0;$col<=$colomncount_number;$col++){
                     $cellVal=null;
                     $cellColor=null;
                     $getCoordinate  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                     $cellVal = $worksheet->getCell($getCoordinate.$row)->getFormattedValue();
                     $ColIndex = $getCoordinate;
                     $timeColumns = ["L","M","N","O","P","S","T","U"];
                    if(!empty($worksheet->getCell($ColIndex.$row)->getValue()) && $row!=5 && in_array($ColIndex,$timeColumns)){
                        $expl = explode("/",$worksheet->getCell($ColIndex.$row)->getFormattedValue());
                        $unixTimeStamp = mktime(0,0,0,$expl[1],$expl[0],"20".$expl[2]);
                        $cellVal= date("d.m.",$unixTimeStamp);
                    }
                    if($cellVal=="Standard"){$cellColor = "bg-primary text-white";}
                    if($cellVal=="Süper Exp" || $cellVal=="Express" || $cellVal=="Waiting For Ready Confirmation"){$cellColor = "bg-danger text-white";}
                    if($cellVal=="On the Road" || $cellVal=="Loading, in progress" || $cellVal=="Loading, completed"){ $cellColor = "bg-primary text-white";}
                    if($cellVal=="Delivered"){ $cellColor = "bg-info text-white";}
                    if($cellVal=="Disposition plan is completed"){$cellColor = "bg-danger text-white";}
                    if($cellVal=="Delivery, in progress"){$cellColor = "bg-dark text-light";}
                    if($cellVal == "Loading, confirmed"){$cellColor = "bg-warning-orange text-white";}
                    if($cellVal == "Transferring between facilities" || $cellVal=="Collection/pick-up, completed" ){$cellColor = "bg-warning text-white";}
                    $table .= "<td class='pl-1 pr-2 small $cellColor $boldCell' $colspan>";
                     if($row==4){
                         $table .= $ColIndex;
                     }
                     if($cellVal=="Click here." || $cellVal=="Click here"){
                         $table .= "<a target='_blank' href=".$worksheet->getHyperlink($ColIndex.$row)->getUrl().">$cellVal</a>";
                     }
                     else{
                         $table .= $cellVal;
                     }   
                     $table .= "</td>";
                }
                $table .= "</tr>";
            }	
            $table .= "</table>";
            $cellVal="";
            return $table;
    }
    public function getReferenceTable(){
        $pfad=$this->dbpath."extern/ekol/";
        return $this->referenceTable($pfad);
    }
    public function uploadPreregisterInformation($werknummer){
        if(isset($_REQUEST['upload_zoll_information'])){
            $o = new AvisierungEkol($werknummer,"../");
            $kennzeichen = $_REQUEST['folder'];
            $o->uploadInformation($kennzeichen);
        }
    }
    public function uploadPreregisterInformationFromDashboard($werknummer){
        if(isset($_REQUEST['upload_preregister_information'])){
            $success=null;
            $kennzeichen = $_REQUEST['folder'];
            $returnURI = $_REQUEST['returnURI'];
            $success = "?upload_information=failed";
            if(empty($kennzeichen)){
                header("location:".$returnURI."?upload_information=failed");
                exit;
            }
            switch($kennzeichen){
                case "hegelmann":
                    require_once 'AvisierungHegelmann.php';
                    $o = new AvisierungHegelman($werknummer,"../");
                    $o->uploadInformation($kennzeichen);
                    $success = "?upload_information=success";
                    header("location:".$returnURI.$success);
                break;
                case "ekol":
                    require_once 'AvisierungEkol.php';
                    $o = new AvisierungEkol($werknummer,"../");
                    $o->uploadInformation($kennzeichen);
                    $success = "?upload_information=success";
                    header("location:".$returnURI.$success);
                break;
                case "versandtransport":
                    require_once 'AvisierungVersantransport.php';
                    $o = new AvisierungVersantransport($werknummer,"../");
                    $o->uploadInformation($kennzeichen);
                    $success = "?upload_information=success";
                    header("location:".$returnURI.$success);
                break;
                case "ICOM":
                    
                    require_once 'AvisierungICOM.php';
                    $o = new AvisierungICOM($werknummer,"../");
                    $o->uploadInformation($kennzeichen);
                    $success = "?upload_information=success";
                    header("location:".$returnURI.$success);
                break;
                case "sapsource":
                    require_once 'AvisierungSAP.php';
                    $o = new AvisierungSAP($werknummer,"../");
                    $o->uploadInformation($kennzeichen);
                    $success = "?upload_information=success";
                    header("location:".$returnURI.$success);
                break;
            }
        }
    }
    private function getTableInformations($frzNummer){
        $q="SELECT object_frz FROM ".$this->tb;
        $results = $this->connect->select($q);
        foreach($results as $result){
            foreach($result as $array){
                $toArray = json_decode($array,true);
                foreach($toArray as $kenz=>$data){
                    if($kenz==$frzNummer){
                        return $data;
                    }
                }
            }
        }
    }
    private function goodsInformation($frzNummer){
        $dataArray = $this->getTableInformations($frzNummer);
        $table = '<div class="goods-data overflow-auto">';
        $table .= '<table class="table table-striped ">
            <thead>
            <tr class="bg-light">
                <th class="bg-light">Lieferant</th>
                <th class="bg-light">Reference</th>
                <th class="bg-light">Sendung-Nr.</th>
                <th class="bg-light">Anz. Colli</th>
                <th class="bg-light">Gewicht</th>
                <th class="bg-light">Lieferpapiere</th>
            </tr>
            </thead>
            <tbody class="table table-striped">';
            if(empty($dataArray)){
                $table .='<tr><td colspan="6" class="text-center">keine Daten über die Sendung vorhanden</td></tr>';           
            }
            foreach($dataArray as $knz=>$array){
                $table .= '<tr>';
                if(empty($array['sendungsnr'])){
                    $table .= '<td colspan="6" class="alert alert-info text-center p-3">keine Daten</td>';
                    
                }else{
                $table .= '<td>'.$array['sender'].'</td>
                <td class="smaller">'.$array['ConsigneeReference'].'</td>
                <td>'.$array['sendungsnr'].'</td>
                <td>'.$array['colli'].' '.$array['packaging'].'</td>
                <td>'.$array['weight'].' </td>
                <td><a href="'.$array['attachment'].'" target="_blank">Klick</a></td>';
            }
            echo '</tr>';
               
            }
        $table .= '</tbody></table></div>';
        echo $table;
    }
    private function getInformFromPreRegisterData(){
        $frzNummer = $_REQUEST['getInformFromPreRegisterData'];
        $this->goodsInformation($frzNummer);
    }
    private function ajaxSendungsnummer(){
        $q="SELECT * FROM tracker";
        $results = $this->connect->select($q);
        if(empty($results)){
            echo "[{}]";
        }
        if($_REQUEST['filter']=="sendungsnummer"){

            foreach($results as $result){
                foreach($result as $array){
                    $toArray = json_decode($array,true);
                    foreach($toArray as $kenz=>$data){
                        foreach($data as $values){
                            if(!empty($values['sendungsnr'])){
                                $arr[]=$values['sendungsnr'];
                            }
                        }
                    }
                }
            }
        }

        if($_REQUEST['filter']=="lieferant"){
            foreach($results as $result){
                foreach($result as $array){
                    $toArray = json_decode($array,true);
                    foreach($toArray as $kenz=>$data){
                        foreach($data as $values){
                            if(!empty($values['sender'])){
                                $arr[]=$values['sender'];
                            }
                        }
                    }
                }
            }
        }
        if($_REQUEST['filter']=="autokennzeichen"){
            foreach($results as $result){
                foreach($result as $array){
                    $toArray = json_decode($array,true);
                    foreach($toArray as $kenz=>$data){
                        if(!empty($kenz)){
                            $arr[]=$kenz;
                        }
                    }
                }
            }
        }
        echo json_encode($arr);
    }
    private function findDataByFilter($inputData, $filter){
        $q="SELECT * FROM tracker";
        $results = $this->connect->select($q);
        if(empty($results)){
           return [];
        }
        if($filter=="sendungsnummer"){
            foreach($results as $result){
                foreach($result as $array){
                    $toArray = json_decode($array,true);
                    foreach($toArray as $kenz=>$data){
                        foreach($data as $values){
                            if($values['sendungsnr']==$inputData){
                                return [$result['kennzeichen']=>$values];
                            }
                        }
                    }
                }
            }
        }
        if($filter == "lieferant"){
            foreach($results as $result){
                foreach($result as $array){
                    $toArray = json_decode($array,true);
                    foreach($toArray as $kenz=>$data){
                         foreach($data as $values){
                            if(!empty($values['sender']) && trim($values['sender'])==trim($inputData)){
                                 $arr[$result['kennzeichen']][] = $values;
                            }
                         }
                    }
                }
            }
        }
        if($filter == "autokennzeichen"){
            foreach($results as $result){
                foreach($result as $array){
                    $toArray = json_decode($array,true);
                    foreach($toArray as $kenz=>$data){
                        if($kenz==$inputData){
                         $arr[$result['kennzeichen']] = $data;
                        }
                    }
                }
            }
        }
        return $arr;
    }
    private function sendungsnummerByAutoNumber($autonummer){
        $timeRange = time()-86400;
        $q="SELECT * FROM tracker";
        $results = $this->connect->select($q);
            if(empty($results)){
                return [];
            }
            foreach($results as $result){
                foreach($result as $array){
                    $toArray = json_decode($array,true);
                    foreach($toArray as $kenz=>$data){
                        if($kenz==$autonummer){
                            foreach($data as $values){
                                if($values['currentArrivalStamp']>$timeRange){
                                    return [$result['kennzeichen']=>$data];
                                }
                            }
                        }
                    }
                }
            }
    }
    public function ajaxGetGoodsInformation(){
        $frzNummer = $_REQUEST['ajaxGetGoodsInformation'];
        $this->goodsInformation($frzNummer);
    }
    public function ajaxGetInformFromPreRegisterData(){
        $this->getInformFromPreRegisterData();
    }
    public function ajaxFindSendungsnummer(){
        $this->ajaxSendungsnummer();
    }
    public function getSendungsData(){
        if(isset($_REQUEST['getSendungsData'])){
            $getSendungsData= $_REQUEST['getSendungsData'];
            $filter = $_REQUEST['filter'];
            $array = $this->findDataByFilter($getSendungsData, $filter);
            if(empty($array)){
                echo '<div class="alert alert-danger text-center">kein Resultat</div>';
            }
           if($filter=="sendungsnummer"){
                  foreach($array as $spedition=>$array){
                    echo '<div class="overflow-auto p-4"><table class="table p-4">
                    <tr><td>Spedition</td><td>'.ucfirst($spedition).'</td></tr>';
                    foreach($array as $key=>$value){
                        if($key=="currentArrivalStamp"){
                            $value = date("d.m.y",$value);
                        }
                        if($key=="attachment" && !empty($value)){
                            $value = "<a href='$value' target='_blank'>Dokumenten</a>";
                        }
                        if($key=="geoposition" && !empty($value)){
                            $value = "<a href='$value' target='_blank'>Geo-Position</a>";
                        }
                        echo '<tr>';
                        echo '<td>'.ucfirst($key).'</td>';
                        echo '<td>'.$value.'</td>';
                        echo '</tr>';
                    }
                    echo '</table></div>';
                }
            }
            if($filter=="lieferant"){
                  foreach($array as $spedition=>$arrays){
                    echo '<div class="overflow-auto p-4">';
                    foreach($arrays as $values){
                    echo '<table class="table p-4 mb-5">
                    <tr><td style="width:25%">Spedition</td><td>'.ucfirst($spedition).'</td></tr>';
                        foreach($values as $key=>$value){
                        if($key=="currentArrivalStamp"){
                            $value = date("d.m.y",$value);
                        }
                        if($key=="attachment" && !empty($value)){
                            $value = "<a href='$value' target='_blank'>Dokumenten</a>";
                        }
                        if($key=="geoposition" && !empty($value)){
                            $value = "<a href='$value' target='_blank'>Geo-Position</a>";
                        }
                        echo '<tr>';
                        echo '<td>'.ucfirst($key).'</td>';
                        echo '<td>'.$value.'</td>';
                        echo '</tr>';
                        }
                      echo "</table>";
                    }
                    echo '</div>';
                }
           }
           if($filter=="autokennzeichen"){
                foreach($array as $spedition=>$arrays){
                    echo '<div class="overflow-auto p-4">';
                    foreach($arrays as $values){
                        echo '<table class="table p-4 mb-5">
                        <tr><td style="width:25%">Spedition</td><td>'.ucfirst($spedition).'</td></tr>';
                        foreach($values as $key=>$value){
                        if($key=="currentArrivalStamp"){
                            $value = date("d.m.y",$value);
                        }
                        if($key=="attachment" && !empty($value)){
                            $value = "<a href='$value' target='_blank'>Dokumenten</a>";
                        }
                        if($key=="geoposition" && !empty($value)){
                            $value = "<a href='$value' target='_blank'>Geo-Position</a>";
                        }
                        echo '<tr>';
                        echo '<td>'.ucfirst($key).'</td>';
                        echo '<td>'.$value.'</td>';
                        echo '</tr>';
                        }
                      echo "</table>";
                    }
                    echo '</div>';
                }
            }
        }
    }
    public function getSendungsDataByNumber(){
        if(isset($_REQUEST['getSendungsDataByNumber'])){
            $autonummer= $_REQUEST['getSendungsDataByNumber'];
            $array = $this->sendungsnummerByAutoNumber($autonummer);
            if(empty($array)){
                echo '<div class="alert alert-danger text-center">kein Resultat</div>';
            }
            foreach($array as $spedition=>$arrays){
                foreach($arrays as $array){
                    echo '<div class="overflow-auto p-4">';
                    echo '<table class="table p-4">
                        <tr><td>Spedition</td><td>'.ucfirst($spedition).'</td></tr>';
                        foreach($array as $key=>$value){
                            if($key=="currentArrivalStamp"){
                                $value = date("d.m.y",$value);
                            }
                            if($key=="attachment" ){
                                $value = "<a href='$value' target='_blank'>Dokumenten</a>";
                            }
                            if($key=="geoposition"){
                                $value = "<a href='$value' target='_blank'>Geo-Position</a>";
                            }
                            echo '<tr>';
                            echo '<td style="width:25%">'.ucfirst($key).'</td>';
                            echo '<td>'.$value.'</td>';
                            echo '</tr>';
                        }
                    echo '</table></div>';
                }
            }

        }
    }
}