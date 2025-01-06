<?php
require_once '../class/Controller.php';
$o = new Controller();
$readDB=$o->getUnloadPlaceData("../");
extract($_REQUEST);

?>

<form action="class/action">
    <div class="row mb-5">
        <div class="col-12">
            <h3><?=$Nummer?></h3>
            <h5>Weiterleitung hinzufügen</h5>
        </div>


        <div class="col-8">
            <select class="form-control" name="next_platz" required>
                <option value="">Abladestelle wählen</option>
                <?php foreach($readDB as $data):?>
                <option value="<?=$data['Platz']?>"><?=$data['Platz']?></option>
                <?php endforeach;?>
            </select>
            <input type="hidden" name="sendtonextstep" value="<?=$rfnum?>:<?=$Nummer?>">
            <input type="hidden" name="returnURI" value="<?=$returnURI?>">
        </div>
        <div class="col-4 ps-0"><button type="submit" class="btn p-2 text-light btn-info">hinzufügen</button></div>
    </div>
</form>