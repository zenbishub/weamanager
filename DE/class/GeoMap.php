<?php
session_start(); 
class GeoMap{

    public function __construct($werknummer=730){
        if(empty(session_id())){
            session_start();
        }
        $_SESSION['werknummer']=$werknummer;
        error_reporting(0);
    }
    public function getScannerData(){
        if(isset($_REQUEST['getscannerdata'])){
            require '../class/Sqlite.php';
            $liteDB= new Sqlite($_SESSION['werknummer'],"../");
            $q = "SELECT * FROM scanner WHERE user='pforte' ORDER BY id";
            $results = $liteDB->sqliteSelect($q);
            echo json_encode($results);
        }
    }
    public function getIPPosition(){
        if(isset($_REQUEST['getIPPosition'])){
            $IP = $_REQUEST['getIPPosition'];
            $prepare = substr($IP,-6);
            require '../class/Sqlite.php';
            $liteDB= new Sqlite($_SESSION['werknummer'],"../");
            $q = "SELECT * FROM scanner WHERE user='pforte' ORDER BY id";
            $read = $liteDB->sqliteSelect($q);

            foreach($read as $array){
                if($array['IP']==$IP){

                $calcY = (10044405-$array['coordYlongtitude'])/8.8;
                $calcX = (48398559-$array['coordXlatitude'])/6.8;
                    if($calcY<1080){
                        echo $calcY."/".$calcX;
                    }
                    if($calcY>1080){
                       echo "0/0";
                    }
                }
            }
            if(empty($readEntladefluess)){
                echo "/".$prepare;
            }
        }
    }
    private function getRFnummerData($ip, $liteDB){
            $q="SELECT object FROM traffic";
           
            $results = $liteDB->sqliteSelect($q);
            foreach($results as $result){
                $toArray = json_decode($result['object'],true);
                $replace = str_replace("Nr.: ","",$toArray['scanner']);
                if($ip==$replace){
                    return $toArray['rfnum']."/".$toArray['Firma'];
                }
                
            }

    }
    public function getLegende(){
        if(isset($_REQUEST['getLegende'])){
            require '../class/Sqlite.php';
            $liteDB= new Sqlite($_SESSION['werknummer'],"../");
            $q = "SELECT * FROM scanner WHERE user='pforte' ORDER BY id";
            $read = $liteDB->sqliteSelect($q);
            $lf=1;
            
            foreach($read as $array){
                $prepare = substr($array['IP'],-6);
                $ipOp = str_replace(".","",$array['IP']);
                $calcY = (10044405-$array['coordYlongtitude'])/9.1;
                $pincolor = "bg-warning";
                $rfnumData = $this->getRFnummerData($prepare,$liteDB);
                $wartenummer="";
                    if(!empty($rfnumData)){
                        $pincolor = "bg-danger";
                    }
                    echo "<tr>
                        <td class='pt-0 pb-0'>".$lf++.".</td>
                        <td class='pt-0 pb-0'> <span class='pin-legende mr-2 pointer ".$pincolor."' title='$rfnumData' data-index='".$ipOp."'></span>
                        </td>
                        <td class='pt-0 pb-0 align-middle'>
                             ".substr($array['IP'],6)."<span class='small'>$wartenummer</span>
                        </td> ";
                        if(empty($array['latitude'])){
                            echo "<td class='pt-0 pb-0'></td>";
                        }
                        if(!empty($array['latitude'])){
                        echo "<td class='pt-0 pb-0'>
                        <a href='https://maps.google.com/maps?q=".$array['latitude'].",".$array['longtitude']."&z=17&output=embed&t=k&iwloc=addr' 
                        class='open-map-iframe'
                        data='".$ipOp."'>in Karte</a>
                        </td>
                        ";
                        }
                    echo "</tr>";
            }
        }
    }
    public function insertDB(){
        require_once 'class/connect.php';
        $db = new connect();
        require '../class/Sqlite.php';
        $liteDB= new Sqlite($_SESSION['werknummer'],"../");
        $q = "SELECT * FROM scanner ORDER BY id";
        $read = $liteDB->sqliteSelect($q);
        foreach($read as $array){
           echo $q="INSERT INTO wareneingang.dbo.scanner(
                sNummer,MacAdr,IP,laden,batterie,handshake,coordXlatitude,coordYlongtitude,latitude,longtitude
                )VALUES(
                    '".$array['sNummer']."',
                    '".$array['MacAdr']."',
                    '".$array['IP']."',
                    '".$array['laden']."',
                    ".$array['batterie'].",
                    '".$array['handshake']."',
                    '".$array['coordXlatitude']."',
                    '".$array['coordYlongtitude']."',
                    '".$array['latitude']."',
                    '".$array['longtitude']."'
                )";
               echo $db->query($q);
               echo "<br>";
        }
    }
}

$o = new GeoMap();
$o->getScannerData();
$o->getIPPosition();
$o->getLegende();