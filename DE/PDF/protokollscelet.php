<?php
$create = null;
$stapler = null;
extract($_REQUEST);
require_once '../class/connect.php';
        $database = "wareneingang";
        $o=new connect();
        $werknummer = $_SESSION['werknummer'];
        $q="SELECT object_runleaf FROM ".$database.".dbo.werk_$werknummer WHERE id=$create";
        $temp = $o->select($q);
        if(empty($create)){
            exit; 
         }
        $arr = json_decode($temp[0]['object_runleaf'],true);
        
        $Protokoll_WA = json_decode($arr['Protokoll_WA'],true);
        if(!empty($arr['Reklamation'])){
            $Reklamation = $arr['Reklamation'];
        }
        
        $wa_buro = json_decode($arr['WA_Buro'],true);
        if(!empty($arr['Stapler'])){
            $stapler = $arr['Stapler']['BMI-Nummer'];
            unset($arr['Stapler']);
        }
        $gone = date("d.m.y, H:i",$arr['gone']);
        unset($arr['rfnum']);
        unset($arr['timestamp']);
        unset($arr['Reklamation']);
        unset($arr['Protokoll_WA']);
        unset($arr['WA_Buro']);
        unset($arr['gone']);
        
        unset($arr['alarm']);
        
        
?>
        <table class="border-bottom margin-auto">
            <tr>
                <td colspan="3">
                    <h2>EvoBus Kontroll- und Laufschein</h2><h4>Anmeldedaten</h4>
                </td>
            </tr>
            
            <?php foreach($arr as $key=>$value):?>
                <tr>
                    <td class="border">
                        <?=ucfirst($key)?>
                    </td>
                    <td class="right border" colspan="2">
                        <?php print_r($value)?>
                    </td>
                    
                </tr>
            <?php endforeach;?>
            <tr>
                <td class="border">
                    Stapler
                </td>
                <td class="right border" colspan="2">
                    <?=$stapler?>
                </td>
            </tr>
            <tr>
                <td class="border">
                    Werk verlassen
                </td>
                <td class="right border" colspan="2">
                    <?=$gone?>
                </td>
            </tr>
            <tr>
                <td>
                    <table class="nebentable">
                        <tr>
                            <th colspan="3"><h4>Warenannahme</h4></th>
                        </tr>
                        <?php if(!empty($wa_buro)):?>
                        <tr>
                            <th colspan="2">Punkte</th>
                            <th>festgestellt</th>
                        </tr>
                        <tr>
                            <td>1.</td>
                            <td><?=$wa_buro['Frage_2']?></td>
                            <td class="center"><?=$wa_buro['check_2']?></td>
                        </tr>
                        <tr>
                            <td>2.</td>
                            <td><?=$wa_buro['Frage_3']?></td>
                            <td class="center"><?=$wa_buro['check_3']?></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="right">D-Stempel <?=$wa_buro['person_sign']?></td>
                        </tr>
                        <?php endif;?>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3">
            <table class="nebentable">
                <tr>
                    <th colspan="3"><h4>Ladungsicherung</h4></th>
                </tr>
                <?php if(!empty($Protokoll_WA)):?>
                <tr>
                    <th colspan="2">Punkte</th>
                    <th>festgestellt</th>
                    </tr>
                        <tr>
                            <td>1.</td>
                            <td><?=$Protokoll_WA['Frage_2']?></td>
                            <td class="center"><?=$Protokoll_WA['check_2']?></td>
                            </tr>
                        <tr>
                            <td>2.</td>
                            <td><?=$Protokoll_WA['Frage_3']?></td>
                            <td class="center"><?=$Protokoll_WA['check_3']?></td>
                        </tr>
                        <tr>
                            <td>3.</td>
                            <td><?=$Protokoll_WA['Frage_4']?></td>
                            <td class="center"><?=$Protokoll_WA['check_4']?></td>
                        </tr>
                        <tr>
                            <td>4.</td>
                            <td><?=$Protokoll_WA['Frage_5']?></td>
                            <td class="center"><?=$Protokoll_WA['check_5']?></td>
                        </tr>
                        <tr>
                            <td>5.</td>
                            <td><?=$Protokoll_WA['Frage_6']?></td>
                            <td class="center"><?=$Protokoll_WA['check_6']?></td>
                        </tr>
                        
                    <tr>
                        <td colspan="3" class="right">D-Stempel <?=$Protokoll_WA['person_sign']?></td>
                    </tr>
                    <?php endif;?>
            </table>
                </td>
                </tr>
                <tr>
                    <td colspan="3">
                        
                    <table class="nebentable">
                    <tr>
                        <th colspan="3"><h4>Reklamation</h4>
                    
                    </th>
                    </tr>
                    <tr>
                    <th colspan="2">Punkte</th>
                    <th>festgestellt</th>
                    </tr>
                        <tr>
                            <td>1.</td>
                            <td>Beschreibung</td>
                            <td class="center"><?=$Reklamation['reklamation_beschreibung'] ?></td>
                            </tr>
                        <tr>
                            <td>2.</td>
                            <td>Bilder</td>
                            <td class="center">
                                <?php
                                        foreach($Reklamation['reklamation_bilder'] as $bild){
                                        echo "<img src='../db/".$_SESSION['werknummer']."/reklamation/$bild' class='img-fluid'>";
                                        echo "<br>";
                                        }
                                    
                                ?>
                            </td>
                        </tr>
                    
                    </table>
                    </td>
            </tr>
        </table>
   

            
    