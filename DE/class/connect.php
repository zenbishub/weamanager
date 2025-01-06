<?php

class connect{
	public $KT;
	public $MT;
	public $currentJahr;
	public $tage;
	public $tageEN;
	public $tageDE;
	public $monate;
	public $monateEN;
	public $monate_anzahl;
	public $wochentage;
	public $timeStempel;
    public $serverName;
	public $database;
	public $username;
	public $password;
	public $dbConnection;
	public $arrayLagerName;
	public $arrayKommLager1;
	public $arrayKommLager2;
	public $arrayAutomatikLager;
	public $recordRange;
	public $lagerNames;
	public $rangeOverload;
	public $ldaphost;
	public $ldapdn;
	public $ldapbase;
	public $ldapdomain;
	public $ldapuser;
	public $ldappass;

public function __construct(){
		if(!isset($_SESSION)){session_start();}
		$this->KT = array();
		$this->tage = 365;
		$this->monateEN = array("January","February","March","April","May","June","July","August","September","October","November","December");
		$this->monate = array("Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");
		$this->monate_anzahl = array("01","02","03","04","05","06","07","08","09","10","11","12");
		$this->wochentage = array("Mon","Tue","Wed","Thu","Fri","Sat","Son");
		$this->tageEN = array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");
		$this->tageDE = array("Montag","Dienstag","Mittwoch","Donnerstag","Freitag","Samstag");
		$this->currentJahr=date("Y");
		if(!isset($_SESSION['setorgjahr'])){
			$_SESSION['setorgjahr']=$this->currentJahr;
		}
		$checkip = explode(".",$_SERVER['REMOTE_ADDR']);
        switch($checkip[0]){
            case "192":
                $this->serverName = "ESERVER\\SQLEXPRESS";
                $this->database = "wareneingang";
                $this->username = "Lerntube";
                $this->password = "Hazic69nurich";
				$this->ldaphost = "zenbis.de";
				$this->ldapbase = "DC=zenbis,DC=de";
				$this->ldapdn = "ZENBIS";
				$this->ldapdomain = "@zenbis.de";
				$this->ldapuser = "Administrator";
				$this->ldappass = "Hazic69nurich";
            break;
            default:
                $this->serverName = "SEVOM011N030";
                $this->database = "wareneingang";
                $this->username = "lerntube";
                $this->password = "Sysadmin2018";
				$this->ldaphost = "emea.corpdir.net";
				$this->ldapbase = "DC=emea,DC=corpdir,DC=net";
				$this->ldapdn = "EMEA";
				$this->ldapdomain = "@emea.corpdir.net";
				$this->ldapuser = "WHAZENB";
				$this->ldappass = "Hazicnurich1982";
            break;
        }
	
		$this->dbConnection = $this->doConnect();
		$this->timeStempel = time();
		error_reporting(0);
}
public function doConnect(){
	$serverName = $this->serverName;
	$connectionInfo = array( "Database"=>$this->database, "UID"=>$this->username, "PWD"=>$this->password);
	return sqlsrv_connect( $serverName, $connectionInfo );
}
public function query($q){
	if( $this->dbConnection === false ){
		return 0;
	}
	$stmt = sqlsrv_query($this->dbConnection, $q );
	$num = sqlsrv_rows_affected( $stmt);
	return $num;
}
public function select($arr){
	$done=sqlsrv_query($this->dbConnection, $arr);
	if( $done === false ) {
		return [];
	}else{
		while( $row = sqlsrv_fetch_array( $done, SQLSRV_FETCH_ASSOC) ){
			$array[]=$row;
		}
	}
	return $array;
}
public function numrows($q){
	if( $this->dbConnection === false ){
		return 0;
	}
		$options =  array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
		$stmt = sqlsrv_query($this->dbConnection, $q , array(), $options );
		$num = sqlsrv_num_rows( $stmt );
		return $num;
}
public function affectedrows($q){
	if( $this->dbConnection === false ){
		return 0;
	}
	$params = array("updated data", 1);
	$stmt = sqlsrv_query( $this->dbConnection, $q, $params);
	$rows_affected = sqlsrv_rows_affected( $stmt);
		if( $rows_affected === false) {
			//die( print_r( sqlsrv_errors(), true));
		}elseif( $rows_affected == -1) {
			return "No information available.<br />";
		}else{
			return $rows_affected." rows were updated.<br />";
		}
}
public function shortText($text, $r){
    $tmp = substr($text,0,$r);
	if(strlen($text)>$r){
		return $tmp." ...";
	}
    return $tmp;
}
public function renameDatum($day=""){
	$i=0;
	foreach($this->tageEN as $days){
		if($days==$day){
		$day = $this->tageDE[$i];
		}
		$i++;
	}
	return substr($day,0,2);
}
public function alert(){
	if(isset($_REQUEST['alert'])){
		switch($_REQUEST['alert']){
			case "success":
				echo "<div class='alert-success small text-center p-2 fixed-bottom'>aktion erfolgreich</div>";
			break;
			case "failed":
				echo "<div class='alert-danger small text-center p-2 fixed-bottom'>aktion nicht erfolgreich</div>";
			break;
			case "failedpremission":
				echo "<div class='alert-danger small text-center p-2 fixed-bottom'>keine Rechte für diesen Bereich</div>";
			break;
		}
	}
}
public function sendEmail($empfaenger,$from,$betreff,$nachricht){
	$header = "From: ".$from."\r\n" .
		//"Reply-To:". $replyTo . "\r\n" .
		"X-Mailer: PHP/" . phpversion()." \r\n".
		"Content-type: text/html; charset=utf-8\n";
	return mail($empfaenger,$betreff,$nachricht,$header);
  }
}
?>