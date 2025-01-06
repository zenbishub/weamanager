<?php
require_once 'iPreregister.php';

class AvisierungSAP implements IPreregister{
    private $dbpath;
    private $tb="tracker";
    private $connect;
    public function __construct($werknummer,$path=""){
        require_once 'connect.php';
        require_once 'PHPExcel.php';
        $this->connect = new connect();
        $this->dbpath=$path."db/".$werknummer."/";
        error_reporting(0);
    }
    public function insertTableInformations($kennzeichen){
        $pfad=$this->dbpath."extern/$kennzeichen/AvisSAP.xlsx";
        require_once 'PHPExcel.php';
        $exelReader= PHPExcel_IOFactory::createReaderForFile($pfad);
        $excelObj = $exelReader->load($pfad);
        $worksheet=$excelObj->getSheet('0');
        $lastRow = $worksheet->getHighestRow();
            for($row=2;$row<=$lastRow;$row++){
                $dispostelle = $worksheet->getCell("D".$row)->getValue();
                $serviceType = $worksheet->getCell("B".$row)->getValue();
                $ConsigneeReference = $worksheet->getCell("M".$row)->getValue();
                $abladestellID = $worksheet->getCell("M".$row)->getValue();
                $knzNummer = "";
                $sendNummer = "";
                $empfaenger = "";
                $colliAnzahl = "";
                $packaging = "";
                $attachment = "";
                $vehiclePos = "";
                $status = "";
                $weight = "";
                $transport_for="";
                $unixTimeStamp = time();
                if($dispostelle==1000){
                    $transport_for = "Versand Werk 5";
                }
                $estUnloadTime = time();
                if(!empty($serviceType)){
                    $dataArray[$serviceType]=[
                    "Dienstleister"=>$serviceType,
                    "abladestellID"=>$abladestellID,
                    "Empfänger"=>$empfaenger,
                    "kennzeichen"=>$knzNummer,
                    "sendungsnr"=>$sendNummer,
                    "colli"=>$colliAnzahl,
                    "packaging"=>$packaging,
                    "weight"=>$weight,
                    "attachment"=>$attachment,
                    "geoposition"=>$vehiclePos,
                    "status"=>$status,
                    "currentArrivalStamp"=>$unixTimeStamp,
                    "ConsigneeReference"=>$abladestellID,
                    "transport_for"=>$transport_for,
                    "estUnloadTime"=>date("d.m.y",$unixTimeStamp),
                    ];
                }
            }
            $lieferDaten = json_encode($dataArray);
            $q="UPDATE ".$this->tb." SET  object_frz='$lieferDaten', tstamp=".time()." WHERE kennzeichen='$kennzeichen'";
            return $this->connect->query($q);
    }
    public function uploadInformation($kennzeichen){
        $filename = "AvisSAP.xlsx";
        $pfad=$this->dbpath."extern/$kennzeichen/";
        if(empty($_FILES)){
            exit;
        }
        if(move_uploaded_file($_FILES['infomation_file']['tmp_name'],$pfad.$filename)){
            $this->insertTableInformations($kennzeichen);
            echo "<div class='alert alert-success'>Datei erfolgreich hochgeladen</div>";
        }
        echo "<div class='alert alert-danger'>Fehler beim Upload</div>";
    }
    public function referenceTable($kennzeichen){
        $table ="";
        $pfad=$this->dbpath."extern/$kennzeichen/";
        $file = $pfad."AvisVersandtransport.xlsx";
        $exelReader= PHPExcel_IOFactory::createReaderForFile($file);
        $excelObj = $exelReader->load($file);
        $worksheet=$excelObj->getSheet('0');
        $lastRow = $worksheet->getHighestRow()-1;
        $colomncount = $worksheet->getHighestDataColumn();
        $colomncount_number=PHPExcel_Cell::columnIndexFromString($colomncount)-1;
        $table .= "<table align='center' class='table table-striped bg-white'>";
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
                    $cellVal = $worksheet->getCell(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getFormattedValue();
                    $ColIndex = PHPExcel_Cell::stringFromColumnIndex($col);
                    $timeColumns = ["L","M","N","O","P","S","T","U"];
                    if(!empty($worksheet->getCell($ColIndex.$row)->getValue()) && $row!=5 && in_array($ColIndex,$timeColumns)){
                        $unixTimeStamp = PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCell($ColIndex.$row)->getValue());
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
                        $table .= PHPExcel_Cell::stringFromColumnIndex($col);
                    }
                    if($cellVal=="Click here." || $cellVal=="Click here"){
                        $table .= "<a target='_blank' href=".$worksheet->getHyperlink(PHPExcel_Cell::stringFromColumnIndex($col).$row)->getUrl().">$cellVal</a>";
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
}