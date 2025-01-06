<?php


class ldap{

public function __construct(){

}

private function searchAndFilter($stack,$search,$ldaphost,$ldapuser,$ldapdomain,$ldappass,$ldapbase){
       
        $filter = "($stack=$search)";
        
        $connect = ldap_connect($ldaphost);
        ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($connect, LDAP_OPT_REFERRALS, 0);
        ldap_bind($connect, $ldapuser.$ldapdomain, $ldappass);
        $read = ldap_search($connect, $ldapbase, $filter);
        return  ldap_get_entries($connect, $read);
        //ldap_close($connect);

}
private function win_time_to_unix_time($win_time) {
    if($win_time=="o"){
        return "";
    }
    $unix_time = round($win_time / 10000000) - 11644477200;
    return date("d.m.Y, H:i:s",$unix_time);
}
private function convert_ldap_time($ldap_time, $output_format){
        $format = DateTime::createFromFormat('YmdHis', rtrim($ldap_time, '.0Z'));
        if (!$format) $format = DateTime::createFromFormat('Ymdhis', rtrim($ldap_time, '.0Z'));
        return ($format) ? $format->format($output_format) : false;
}
private function entriesToArray($entries=array()){
        $list  = array();
        unset($entries['count']);
        foreach($entries as $entry){
            unset($entry['count']);
            $list[]=$entry;
        }
    return $list;
}
public function showEntries($stack, $search,$ldaphost,$ldapuser,$ldapdomain,$ldappass,$ldapbase){
    if(empty($search)){
        return [];
    }
    $infos = $this->searchAndFilter($stack, $search,$ldaphost,$ldapuser,$ldapdomain,$ldappass,$ldapbase);
    $arr   = $this->entriesToArray($infos);
        $userArray=[];
        foreach($arr as $i=>$object){
            foreach($object as $key=>$values){
                foreach(array($values) as $value){
                    
                    if($key=="displayname"){
                        $userArray[$i]['anzeigename']=$value[0];
                    }
                    if($key=="givenname"){
                        $userArray[$i]['Vorname']=$value[0];
                    }
                    if($key=="sn"){
                        $userArray[$i]['Name']=$value[0];
                    }
                    if($key=="l"){
                        $userArray[$i]['Werkname']=$value[0];
                    }
                    if($key=="userprincipalname"){
                        $userArray[$i]['userprincipalname']=$value[0];
                    }
                    if($key=="managedobjects"){
                        $userArray[$i]['managedobjects']=$value[0];
                    }
                    if($key=="title"){
                        $userArray[$i]['title']=$value[0];
                    }
                    if($key=="distinguishedname"){
                        $userArray[$i]['distinguishedname']=$value[0];
                    }
                    if($key=="description"){
                        $userArray[$i]['beschreibung']=$value[0];
                    }
                    if($key=="samaccountname"){
                        $userArray[$i]['userid']=$value[0];
                    }
                    if($key=="wwwhomepage"){
                        $userArray[$i]['wwwhomepage']=$value[0];
                    }
                    if($key=="mail"){
                        $userArray[$i]['mail']=$value[0];
                    }
                    if($key=="streetaddress"){
                        $userArray[$i]['adresse']=$value[0];
                    }
                    if($key=="telephonenumber"){
                        $userArray[$i]['telefon']=$value[0];
                    }
                    if($key=="postalcode"){
                        $userArray[$i]['postal']=$value[0];
                    }
                    if($key=="physicaldeliveryofficename"){
                        $userArray[$i]['city']=$value[0];
                    }
                    if($key=="st"){
                        $userArray[$i]['land']=$value[0];
                    }
                    if($key=="co"){
                        $userArray[$i]['country']=$value[0];
                    }
                    if($key=="mobile"){
                        $userArray[$i]['mobile']=$value[0];
                    }
                    if($key=="homephone"){
                        $userArray[$i]['homephone']=$value[0];
                    }
                    if($key=="initials"){
                        $userArray[$i]['initials']=$value[0];
                    }
                    if($key=="whencreated"){
                        $userArray[$i]['whencreated']=$this->convert_ldap_time($value[0], "d-m-Y H:i:s");
                    }
                    if($key=="whenchanged"){
                        $userArray[$i]['whenchanged']=$this->convert_ldap_time($value[0], "d-m-Y H:i:s");
                    }
                    if($key=="lastlogontimestamp"){
                        $timestamp=$this->win_time_to_unix_time($value[0]);
                        $userArray[$i]['lastlogontimestamp']=$timestamp;
                    }
                    if($key=="pwdlastset"){
                        $timestamp=$this->win_time_to_unix_time($value[0]);
                        $userArray[$i]['pwdlastset']=$timestamp;
                    }
                    if($key=="manager"){
                        $userArray[$i]['manager']=$value[0];
                    }
                    if($key=="dcxsitecode"){
                        $userArray[$i]['werknummer']=$value[0];
                    }
                    if($key=="dcxcostcenter"){
                        $userArray[$i]['kostenstelle']=$value[0];
                    }
                    if($key=="accountexpires"){
                        $timestamp=$this->win_time_to_unix_time($value[0]);
                        $userArray[$i]['accountexpires']=$timestamp;
                    }
                    if($key=="memberof"){

                        $items = $value[0];
                        $range=20;
                        for($r=0;$r<$range;$r++){
                            if(!empty($value[$r])){
                                $items.= $value[$r]."<br>";
                            }
                        }
                        $userArray[$i]['memberof']=$items; 
                    }
                }
            }
        }
    
        return $userArray;
}
// private function dataView($array, $search=null){
//     if(!empty($search)){
//         echo "<div class=' nav navbar p-2'><h4 colspan='2'>Such-Resultat nach: $search </h4>";
//         echo "<h5>".count($array)." Einträg/e gefunden</h5>";
//         echo "</div>";
//     }
//     echo "<div class='overflow-auto'>";
//         foreach($array as $arrays):
//             echo "<table class='table table-striped'>";
//                 foreach($arrays as $key=>$val):
//                 echo "<tr>
//                     <th class='pt-1 pb-1'>".ucfirst($key)."</th>
//                     <td class='pt-1 pb-1'>$val</td>
//                 </tr>";
//                 endforeach;
//             echo "</table>";
//             echo "<hr>";
//         endforeach;
//     echo "</div>";
// }
// public function serachForm($stack=null,$search=null){
//     $arr=[];
//     $arr = $this->showEntries($stack,$search);
//     echo "<form method='get'>
//     <div class='row p-0 m-0'>
//     <input type='hidden' name='open' value='evousers' class='form-control'>
//     <div class='col-1 form-group ps-0'>
//     <select name='stack'class='form-control'>
//         <option value='samaccountname'>ID</option>
//         <option value='sn'>Name</option>
//     </select>
//     </div>
//     <div class='col-3 form-group ps-0'>
//         <input type='text' name='search' class='form-control' placeholder='Suche'>
//     </div>
//         <div class='col-3 form-group ps-0'>
//             <button type='submit' class='btn btn-info text-white '>suchen</button>
//         </div>
//     </div>
//     </div>
//     </form>";
//     $this->dataView($arr,$search);
// }
}

// $stack=null;
// $search=null;
//     $ldap = new ldap();
// if(isset($_REQUEST['search'])){
//     $search = $_REQUEST['search'];
//     $stack = $_REQUEST['stack'];
// }







