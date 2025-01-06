<?php 

class Comingup{
    private $db;
  public function __construct(){
      require_once 'connect.php';
      $this->db = new connect();
  }
  public function getVoranmeldeData(){
      $q="SELECT * FROM tracker ORDER by id ASC";
      $results = $this->db->select($q);
      $timeRange = time()-86400;
    
      foreach($results as $result){
            $object = json_decode($result['object_frz'],true);
            foreach($object as $autoKNZ=>$arrays){
              foreach($arrays as $array){
                if(!empty($array['currentArrivalStamp']) && $array['currentArrivalStamp']>$timeRange){
                $weight = str_replace(",","",$array['weight']);
                $arr[$array['currentArrivalStamp']][ucfirst($result['kennzeichen'])][$autoKNZ][]=$array['colli']."-".$weight."-".$array['packaging'];
                }
              }
            }
          }
          ksort($arr);
        return $arr;
  }
  public function tableComingUp($dataArrays){
      echo '<table class="table">
              <thead>
                <tr>
                  <th scope="col" class="p-2 bg-transparent">#</th>
                  <th scope="col" class="p-2 bg-transparent">Spedition</th>
                  <th scope="col" class="p-2 bg-transparent">Autokenzeichen</th>
                  <th scope="col" class="p-2 bg-transparent">Colli</th>
                  <th scope="col" class="p-2 bg-transparent">Gewicht</th>
                  <th scope="col" class="p-2 bg-transparent"></th>
                </tr>
              </thead>
              <tbody>';
              foreach($dataArrays as $datum => $dataValues){
                foreach($dataValues as $spedition=>$autonumbers){
                  foreach($autonumbers as $autonumber=>$arrays){
                    $collis = [];
                    $weight = [];
                    foreach($arrays as $data){
                        $exp = explode("-",$data);
                        $collis[]=$exp[0];
                        $weight[]=$exp[1];
                      }
                    $showColli = array_sum($collis);
                    $showWeight = array_sum($weight)." Kg.";
                    if(array_sum($collis)==0){
                      $showWeight = "-";
                    }
                    if(array_sum($weight)==0){
                      $showWeight = "-";
                    }
                        echo '<tr>
                    <th scope="row" class="fs-3">'.date("d.m.",$datum).'</th>
                    <td class="fs-3">'.$spedition.'</td>
                    <td class="fs-3">'.$autonumber.'</td>
                    <td class="fs-3">'.$showColli.'</td>
                    <td class="fs-3">'.$showWeight.'</td>
                    <td class="fs-3"><button class="btn btn-info p-2 text-light open-liefer-info" data="'.$autonumber.'"><i class="ti-info"></i></button></td>
                  </tr>';
                  }
                }
              }
              echo '</tbody>
          </table>';
  }
}