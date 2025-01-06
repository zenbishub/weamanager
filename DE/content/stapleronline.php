<?php
spl_autoload_register(function ($class_name) {
    include '../class/'. $class_name . '.php';
});
    $controller = new Controller();
    $bmidata = $controller->getBMIData("../");
    $online = $controller->getOnlineData("../");

?>
<div class="card p-0 shadow-0">
    <div class="card body p-1">
        <div class="card-header p-1 small font-weight-bold">manueller Auftrag</div>
        <button class="btn  btn-primary col p-2 mb-2 text-white open-maueller-auftrag-dialog" alt="">erstellen</button>
        <button class="btn  btn-primary col p-2 text-white open-maueller-auftrag-dialog-overview"
            alt="">anzeigen</button>
    </div>
</div>
<div class="card p-1">
    <div class="card-body p-0">
        <div class="card-header p-1 small font-weight-bold">angemeldet</div>
        <ul class="list-group p-1 m-0 mb-2">
            <?php  foreach($online as $user):
             foreach($bmidata as $array):
                $expl = explode("&",$array['Plant']);
               
            switch ($array['available']) {
                case "notavailable":
                    $available = "alert-warning-custom";
                    $title= "nicht verfügbar";
                break;
                case "available":
                case "":
                    $available = "alert-success-custom";
                    $title= "verfügbar";
                break;
                default:
                    $available = "alert-success-custom";
                    $title= "verfügbar";
                break;
          }
                $inummer = $expl[2];
            $bgStyle=null; 
            $textlight = null;
            if($user==$array['BMI-Nummer'] && $_SESSION['INUMMER']==$inummer):?>
            <li class="list-group-item p-0 mb-1 ps-1 <?=$available?>" title="<?=$title?>">
                <a href="#" data-bs-toggle="modal" data-bs-target="#pictureviwever"
                    title="<?=$array['BMI-Typ']?> <?=$array['Hersteller']?> <?=$array['BMI-Nummer']?>"
                    class="link p-0 pictureviwever-show" data="<?=$array['BMI-Nummer']?>&Zentrale"
                    alt="db/<?=$_SESSION['werknummer']?>/bmi/<?=$array['BMI-Bild']?>"><?=$array['BMI-Nummer']?></a>
                <?=$controller->getTaskCounts($array['BMI-Nummer']);?>
            </li>
            <?php endif;?>
            <?php endforeach;?>
            <?php endforeach;?>
        </ul>
        <div class="card-header p-1 small font-weight-bold">alle</div>
        <ul class="list-group p-1 m-0">
            <?php 
            
                foreach($bmidata as $array):
                    
                    $expl = explode("&",$array['Plant']);
                    $inummer = $expl[2];
                    if(!in_array($array['BMI-Nummer'],$online) && $_SESSION['INUMMER']==$inummer):?>
            <li class="list-group-item p-0 ps-2 mb-1">
                <a href="#" data-bs-toggle="modal" data-bs-target="#pictureviwever"
                    title="<?=$array['BMI-Typ']?> <?=$array['Hersteller']?> <?=$array['BMI-Nummer']?>"
                    class="link p-0 pictureviwever-show" data="<?=$array['BMI-Nummer']?>&Zentrale"
                    alt="db/<?=$_SESSION['werknummer']?>/bmi/<?=$array['BMI-Bild']?>"><?=$array['BMI-Nummer']?></a>

            </li>
            <?php endif;endforeach;?>
        </ul>
        <button class="btn btn-primary p-2 btn-sm col text-white" id="open-stapler-aufgaben" data-bs-toggle="modal"
            data-bs-target="#staplerAufgaben">aufgaben</button>
    </div>

</div>