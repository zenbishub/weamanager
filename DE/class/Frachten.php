<?php 

class Frachten{
    private $dbpath;
    private $tb="tracker";
    private $connect;
    public function __construct($werknummer,$path=""){
        require_once 'connect.php';
        $this->connect = new connect();
        $this->dbpath=$path."db/".$werknummer."/";
        error_reporting(0);
    }
    public function tracksByDepartment(){
        if(isset($_REQUEST['tracksByDepartment'])){
        require_once 'Lieferant.php';
        $lieferant = new Lieferant();
        $dayData = date("d.m.y");
        $arrays = $lieferant->getDataFromTracker();
       
        if(!empty($_REQUEST['dayData'])){
            $dayData=$_REQUEST['dayData'];
            $arrays = $lieferant->getDataFromArchiv($dayData);
        }
            foreach($arrays as $array){
                 $toArray = json_decode($array['object'],true);
                foreach($lieferant->LKWfilter as $filter){
                    if($toArray['Ladung']==$filter){
                        $arr[$toArray['Ladung']][]=1;
                    }
                }
            }
          
            ksort($arr);
        foreach($arr as $key=>$value){
            $data['xAchse'][]=$key;
            $data['yAchse'][]=array_sum($value);
        }
        $data['total']=array_sum($data['yAchse']);
        $data['day']=$dayData;
        echo json_encode($data);
        }
        
    }
}