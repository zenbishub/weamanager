<?php 
    class handScanner{
        private $IP;
        public function __construct(){
            $this->IP=$_SERVER['REMOTE_ADDR'];
        }
        public function handleScannerInfo($path=""){
            if(isset($_REQUEST['handleScannerInfo'])){
                $liteDB= new Sqlite($_SESSION['werknummer'],$path);
                $q="SELECT * FROM scanner";
                $arrays = $liteDB->sqliteSelect($q);
                $last_posession = null;
                unset($_POST['handleScannerInfo']);
                if(!empty($_SESSION['rfnum'])){
                    $q="SELECT object FROM traffic WHERE rfnum=".$_SESSION['rfnum']."";
                    $wnArray = $liteDB->sqliteSelect($q);
                    $last_posession = ", last_posession='".$wnArray[0]['object']."";
                }
                
                if(empty($_POST['chargeStatus'])){
                    $_POST['chargeStatus']="--";
                    $_POST['levelStatus']="--";
                }
               
                foreach($arrays as $scanner){
                    if($scanner['IP']==$this->IP){
                        $q="UPDATE scanner SET laden='".$_POST['chargeStatus']."',batterie = '".$_POST['levelStatus']."', handshake = '".date("d.m. H:i")."' $last_posession'
                        WHERE IP='".$this->IP."'";
                        $liteDB->sqliteQuery($q);
                    }
                }
            }
        }

        private function scannerInfo($rfnum,$scannerIP,$path=""){
            if(empty($scannerIP)){
                return null;
            }
            $readDBstatus = file_get_contents($path."db/".$_SESSION['werknummer']."/entladefluess.json");
            $toArray = json_decode($readDBstatus,true);
            
            foreach($toArray as $key=>$arrays){
                if($arrays['rfnum']==$rfnum){
                    echo "<div class='row '><div class='col-12 p-0'>Gerät ".$scannerIP."</div></div>";
                }
            }

        }

    }