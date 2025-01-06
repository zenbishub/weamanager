<?php
spl_autoload_register(function ($class_name) {
    include '../class/'. $class_name . '.php';
});

$controller = new Controller();
$entladung = $controller->staplerAuftrag("../");
$places = $controller->getLocations("../");

?>
<div class="row">
    <?php foreach($places as $place):?>
    <div class="card p-0 col-6 col-md-4 bg-light border rounded-2 place-puzzle shadow-none overflow-auto">
        <div class="card-header p-1 font-weight-bold small">
            <?=$place['Platz']?>
        </div>
        <?php 
                foreach($entladung as $data):
                    if($place['Platz']==$data['Platz'] && $data['Status']<100):
                      switch($data['Status']):
                        case 50:
                          $bgStyle="bg-danger text-white";
                          break;
                        case 501:
                          $bgStyle="bg-danger text-white";
                          break;
                        case 75:
                          case 80:
                          $bgStyle="bg-success text-white";
                          break;
                          case 100:
                            $bgStyle="bg-warning text-black-50";
                            break;
                          default:
                            $bgStyle="";
                      endswitch;
                  ?>
        <div class="card-body p-0 <?=$bgStyle?> small">
            <div class="p-1">
                <span class="bagde badge-warning pl-1 pr-1 rounded"><?=$data['rfnum']?></span>
                <span><?=$data['Firma']?></span>
                <span class="d-block"><?=$data['Nummer']?></span>
                <?php if(!empty($data['Stapler'])):?>

                <?=$data['Stapler']['Hersteller']?> [<?=$data['Stapler']['BMI-Nummer']?>]
                <?php if(!empty($data['Stapler']['BMI-Bild'])):?>
                <a class="pictureviwever-show pointer text-light"
                    data="<?=$data['Stapler']['BMI-Nummer']?>&<?=$_SESSION['weamanageruser']?>"
                    title="<?=$data['BMI-Typ']?> <?=$data['Stapler']['Hersteller']?> <?=$data['Stapler']['BMI-Nummer']?>"
                    alt="db/<?=$_SESSION['werknummer']."/bmi/".$data['Stapler']['BMI-Bild']?>" data-bs-toggle="modal"
                    data-bs-target="#pictureviwever">
                    <i class="ti-gallery"></i></a>
                <?php endif;
                      endif;?>
                <hr class="m-1">
            </div>
        </div>
        <?php endif; 
            endforeach;?>
    </div>
    <?php endforeach;?>
</div>