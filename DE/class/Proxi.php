<?php 

class Proxi{
    public function relayOn(){
        if(isset($_REQUEST['device'])){
            extract($_REQUEST);
            $url = "http://$ip/$relay/".$action;
            
            echo $ch = curl_init();
            exit;
            curl_setopt($ch, CURLOPT_USERAGENT, 'curl');
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            $json = curl_exec ($ch);
            $result = json_decode($json);
            switch($result->ison){
                case true:
                    echo $area.":open";
                break;
                case false:
                    echo $area.":close";
                    break;
            }
        }
    }
}
$o = new Proxi();
$o->relayOn();