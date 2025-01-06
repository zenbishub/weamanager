<?php 

require_once '../class/Controller.php';
$o = new Controller();
$mAuftraege = $o->getOrderListManuellerAuftrag("../");
?>
<div class="row">
    <div class="col-12">

        <table class="table">
            <tr>
                <th>lf</th>
                <th>Erst.Zeit</th>
                <th>Ressourse-Nr.</th>
                <th>Auftrag</th>
                <th>ToDo</th>
            </tr>
            <?php if(empty($mAuftraege)):?>
            <tr>
                <td colspan="6" class="alert-info text-center p-2">Keine manuelle Aufträge</td>
            </tr>
            <?php endif?>

            <?php $lf=1; foreach($mAuftraege as $auftrag):?>
            <tr>
                <td><?=$lf++?></td>
                <td><?=date("d.m. H:i",$auftrag['timestamp'])?></td>
                <td><?=$auftrag['stapler_for_auftrag']?></td>
                <td><?=$auftrag['umfang']?></td>
                <td><?=$auftrag['todo']?></td>
            </tr>

            <?php endforeach;?>
        </table>
    </div>
</div>