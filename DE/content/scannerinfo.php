<?php

spl_autoload_register(function ($class_name) {
    require_once '../class/'.$class_name . '.php';
});
$controler = new Controller();
$gate = new Maingate();
$scanInfo = $gate->scannerInfo("../");

?>
<table class="table table-striped">
    <tr>
        <th>Nr.</th>
        <th>Scanner</th>
        <th>Belegung</th>
        <th>Zeit</th>
        <th>Laden</th>
        <th>Batterie</th>
        <th>HS</th>
        <th colspan="2"></th>
    </tr>
    <?php foreach($scanInfo as $arrays):
            $user = "";
            $anmeldung="";
            $style="";
            $textColor = "";
            $laden = "";
            $batterie = "";
            $handshake = "";
       if(!empty($arrays)){
            $laden = $arrays['laden'];
            $batterie = $arrays['batterie'];
            $handshake = $arrays['handshake'];
            $scnIndex = substr($arrays['IP'],6);
        }
       
        $data = $gate->scannerStatus($scnIndex, "../");
           
        if(!empty($data)){
            $style = "bg-danger";
            $textColor = "text-white";
            $user = "RF ".$data[0]. " ".$data[2];
            $anmeldung = $data[1];
        }
     
        ?>
    <tr class="<?=$style?>">
        <td class="align-middle <?=$textColor?>"><?=$arrays['id']?></td>
        <td class="align-middle <?=$textColor?>"><?=$scnIndex?></td>
        <td class="align-middle <?=$textColor?>"><?=$user?></td>
        <td class="align-middle <?=$textColor?>"><?=$anmeldung?></td>
        <td class="align-middle <?=$textColor?>"><?=$laden?></td>
        <td class="align-middle <?=$textColor?>"><?=ceil($batterie)?>%</td>
        <td class="align-middle <?=$textColor?>"><?=$handshake?></td>
        <td class="text-end align-middle">
            <span class="me-2"></span><button
                class="btn btn-sm p-1 ps-2 pe-2 btn-info me-2 text-white checkScannerOnline"
                alt="<?=$scnIndex?>">check</button>
        </td>
    </tr>
    <?php endforeach;?>
</table>